# sCAPTCHA

A simple CAPTCHA generator for PHP, using ImageMagick.

## Usage

### Basic

    <?php
        require_once('path/to/scaptcha.class.php');

        $sc = new sCAPTCHA();

        // generate captcha key/string
        $key = $sc->key(); // compare key against user's input

        header('Content-type: image/png');
        echo $sc->captcha();

    // -EOF-

Execute:

    $ php basic.php > image.png

### Advance

    <?php
        require_once('path/to/scaptcha.class.php');

        $sc = new sCAPTCHA();

        // overrides some of the configurations
        $sc->config(array(
            'length' => 5, // captcha key length (string)
            'custom_font' => true, // use custom font, instead of default (Arial)
            'noise' => true, // add background noise
            'store' => true, // save/store generated image, locally
            'store_path' => './test' // where to save? (if not specified, then it'll use the default path: /path/to/scaptcha/store)
        ));

        // generate captcha key/string
        $key = $sc->key(); // compare key against user's input

        echo $sc->captcha(); // if 'store' config (save) is true then this will return the image file name
        echo "\n";

    // -EOF-

Execute:

    $ php advance.php
    8918b69fb85812dbc20cf777104da950.png

## Configuration

### session

Save/store generated CAPTCHA key string on session (boolean)

    $sc->config(array(
        'session' => true
    ));

Default: False

### session_name

Session name (string)

    $sc->config(array(
        'session_name' => 'some-session-name'
    ));

Default: sCAPTCHA

### hash

Hash generated key (boolean)

    $sc->config(array(
        'hash' => false // turn-off hash
    ));

Default: True

### length

CAPTCHA character length

    $sc->config(array(
        'length' => 8
    ));

Default: 5

### table

Characters used in generating random CAPTCHA string

    $sc->config(array(
        'table' => 'abcdefghijklmnopqrstuvwxyz'
    ));

Default: 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ

### readable

Use vowels and consonants for captcha readability (boolean)

    $sc->config(array(
        'readable' => false // turn-off readability
    ));

Default: True

### salt

Salt used for hashing CAPTCHA key

    $sc->config(array(
        'salt' => 'sOmE-rAnd0M-$@lT'
    ));

Default: Null

### font

Font name

    $sc->config(array(
        'font' => 'Courier-New'
    ));

Default: Arial

### height

CAPTCHA image height

    $sc->config(array(
        'height' => 60
    ));

Default: 57

### width

CAPTCHA image width

    $sc->config(array(
        'height' => 300
    ));

Default: 200

### wave

Wave effect, amplitude and length (array)

    $sc->config(array(
        'wave' => array(
            'amplitude' => 3,
            'length' => 60
        );        
    ));

Default: amplitude: 5, length: 50

### swirl

Swirl effect (integer)

    $sc->config(array(
        'swirl' => 5
    ));

Default: 10

### noise

Add (effect) background noise (boolean)

    $sc->config(array(
        'noise' => true // turn-on background noise
    ));

Default: False

### custom_font

Use custom font instead of the default one (boolean)

    $sc->config(array(
        'custom_font' => true // use custom font
    ));

Default: False

### custom_font_name

Custom font name

    $sc->config(array(
        'custom_font_name' => 'georgia.ttf'
    ));

Default: Moms_typewriter.ttf

Fallback: Arial

### font_size

Font size

    $sc->config(array(
        'font_size' => 20
    ));

Default: 35

### format

File format (png, jpeg, or gif)

    $sc->config(array(
        'format' => 'jpeg' // png, jpeg, or gif
    ));

Default: png

Fallback: png

### store

Store/save generated image to a file (boolean)

    $sc->config(array(
        'store' => true
    ));

Default: False

### store_path

Where to store generated image, path (string)

    $sc->config(array(
        'store_path' => './images'
    ));

Default: path/to/scaptcha/store

Fallback: path/to/scaptcha/store
