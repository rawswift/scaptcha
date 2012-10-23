<?php
/**
 * sCAPTCHA - A simple CAPTCHA generator.
 * https://github.com/rawswift/scaptcha
 *
 * Copyright (c) 2012 Ryan Yonzon, <rawswift@gmail.com>
 * Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 */

define('ROOT_DIR', __DIR__);
define('FONT_DIR', __DIR__ . '/fonts/');
define('STORE_DIR', __DIR__ . '/store/');

class sCAPTCHA {

	// configurables (see defaults below)
	private $configurables = array(
			'session', // save generated key on session (boolean)
			'session_name', // session name (string)
			'hash', // hash generated key (boolean)
			'length', // captcha character length
			'table', // characters used in generating random captcha string
			'readable', // use vowels and consonants for captcha readability (boolean)
			'salt', // salt used for hashed key
			'font', // font name
			'height', // captcha image height
			'width', // captcha image width
			'wave', // wave effect, amplitude and length (array)
			'swirl', // swirl effect (integer)
			'noise', // add (effect) background noise (boolean)
			'custom_font', // use custom font instead of the default one (boolean)
			'custom_font_name', // custom font name
			'font_size', // font size
			'format', // file format (png, jpeg, or gif)
			'store', // store generated image to a file (boolean)
			'store_path' // where to store generated image
		);

	// Colors from http://www.imagemagick.org/script/color.php
	private $color_table = array(
			'#FF0000', // red		
			'#0000FF', // blue
			'#008000', // green
			'#000000' // black
		);

	private $format_table = array(
			'png', // PNG
			'jpeg', // JPEG
			'jpg', // JPEG
			'gif' // GIF
		);

	// set default configurations
	private $session = false;
	private $session_name = 'sCAPTCHA';
	private $hash = true;
	private $salt = null; // salt used for hashed key (default none)
	private $length = 5;
	private $table = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	private $readable = true;
	private $noise = false;
	private $custom_font = false;
	private $custom_font_name = 'Moms_typewriter.ttf'; // default custom font
	private $font = 'Arial'; // default font
	private $font_size = 35; // default font size

	// captcha image default size
	private $width = 200;
	private $height = 57;

	// text/image effects
	private $swirl = 10;
	private $wave = array(
			'amplitude' => 5,
			'length' => 50
		);
	private $noise_type = 3;
	private $noise_channel = 3;

	// generated key store
	private $key = null;

	// default image format
	private $format = 'png';
	private $store = false;
	private $store_path = STORE_DIR;

	// instantiate
	public function __construct() {
		// set additional default variables
		$this->wave_default = $this->wave;
		$this->format_default = $this->format;
		$this->store_path_default = STORE_DIR;
	}

	// override default configuration
	public function config($config = null) {
		if (isset($config) && is_array($config)) {
			foreach ($config as $k => $v) {
				if (in_array($k, $this->configurables) && isset($v)) {
					$this->$k = $v;
				}
			}
		}
	}

	// generate random key based on the character table
	public function key() {

		if ($this->readable) {
			$this->key = $this->easyRead();
		} else {
			for ($i = 0; $i <= $this->length; $i++) {
				$this->key .= $this->table{rand(0, (strlen($this->table) - 1))};
			}
		}

		// check if to use session to save generated key
		if ($this->session) {
			// check if session is enabled, if not start session
			if(session_id() == "") {
				session_start();
				// check if generated key need to be hashed
				if ($this->hash) {
					$_SESSION[$this->session_name] = sha1($this->key . $this->salt);
				} else {
					$_SESSION[$this->session_name] = $this->key;
				}
			}
		}

		return $this->key;
	}

	// generate readable challenge string
	// based from http://www.phpsnippets.info/generate-a-password-in-php
	public function easyRead() {
		$vowels = 'aeuyAEUY';
		$consonants = 'bdghjmnpqrstvzBDGHJLMNPQRSTVWXZ';

		$string = '';
		$alt = time() % 2;
		for ($i = 0; $i < $this->length; $i++) {
			if ($alt == 1) {
				$string .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} else {
				$string .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}

		return $string;
	}

	// generate captcha image
	public function captcha() {

		// get generated characters
		if (!is_null($this->key) && isset($this->key)) {
			$text = $this->key;
		} else {
			$text = $this->key(); // generate key
		}

		// create Imagick objects
		$image = new Imagick();
		$draw = new ImagickDraw();

		$font_color = new ImagickPixel($this->color_table{rand(0, (count($this->color_table) - 1))});
		$background = new ImagickPixel('#ffffff'); // white

		// font properties
		if (!$this->custom_font) {
			$draw->setFont($this->font);
		} else {
			// check if custom font file exists
			if (file_exists(FONT_DIR . $this->custom_font_name)) {
				$draw->setFont(FONT_DIR . $this->custom_font_name);
			} else {
				$draw->setFont($this->font); // fallback to the default font
			}
		}

		$draw->setFontSize($this->font_size);
		$draw->setFillColor($font_color);

		// get font metrics for centering text string
		$fm = $image->queryFontMetrics($draw, $text, false);
		$coordinate_x = ($this->width / 2) - ($fm["textWidth"] / 2);
		$coordinate_y = $this->height - ($fm["textHeight"] / 2);

		// create text
		$draw->annotation($coordinate_x, $coordinate_y, $text);

		// create image
		$image->newImage($this->width, $this->height, $background);

		// check if file/image format is valid
		$format_setting = strtolower($this->format);
		if (in_array($format_setting, $this->format_table)) {
			if ($format_setting == 'jpeg') {
				$format_setting = 'jpg';
			}
			$image_format = $format_setting;
		} else {
			$image_format = $this->format_default; // fallback to the default file/image format
		}

		$image->setImageFormat($image_format);
		$image->drawImage($draw);

		// add image effects

		// wave
		if (is_array($this->wave)) {
			$image->waveImage($this->wave['amplitude'], $this->wave['length']);
		} else {
			$image->waveImage($this->wave_default['amplitude'], $this->wave_default['length']);
		}

		// swirl
		$image->swirlImage($this->swirl);

		// background noise
		if ($this->noise) {
			$image->addNoiseImage($this->noise_type, $this->noise_channel);
		}

		// check if generated image will be saved as a file
		if ($this->store) {
			$filename = md5(time()) . '.' . $image_format; // construct file's name

			// check if directory exists
			if (file_exists($this->store_path)) {
				$store_file_path = rtrim($this->store_path, '/') . '/' . $filename;
			} elseif (file_exists($this->store_path_default)) { // try the default store path
				$store_file_path = rtrim($this->store_path_default, '/') . '/' . $filename;
			} else {
				// try creating the default store path
				if (!mkdir(rtrim($this->store_path_default, '/'))) {
					return false;
				}
				$store_file_path = rtrim($this->store_path_default, '/') . '/' . $filename;
			}

			// store/save image file
			file_put_contents($store_file_path, $image);
			
			return $filename; // return only the file's name

		} else {
			// return generated image
			return $image;
		}

	}

}

// -EOF-