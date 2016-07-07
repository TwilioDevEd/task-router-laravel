<?php

/**
 * Version of sprintf for cases where named arguments use python syntax
 *
 * With sprintf: sprintf('second: %2$s ; first: %1$s', '1st', '2nd');
 *
 * With sprintfn: sprintfn('second: %(second)s ; first: %(first)s', array(
 *  'first' => '1st',
 *  'second'=> '2nd'
 * ));
 *
 * @param string $format sprintf format string, with any number of named arguments
 * @param array $args array of [ 'arg_name' => 'arg value', ... ] replacements to
 *         be made
 *
 * @return string|false result of sprintf call, or bool false on error
 */
function sprintfn($format, array $args = array()) 
{
    // map of argument names to their corresponding sprintf numeric argument value
    $arg_nums = array_slice(array_flip(array_keys(array(0 => 0) + $args)), 1);

    // find the next named argument. each search starts at the end
    // of the previous replacement.
    for ($pos = 0;
            preg_match(
                '/(?<=%)\(([a-zA-Z_]\w*)\)/',
                $format,
                $match,
                PREG_OFFSET_CAPTURE, $pos
            );) {
                $arg_pos = $match[0][1];
                $arg_len = strlen($match[0][0]);
                $arg_key = $match[1][0];

                // programmer did not supply a value for the named argument found in
                // the format string
                if (! array_key_exists($arg_key, $arg_nums)) {
                    user_error(
                        "sprintfn(): Missing argument '${arg_key}'",
                        E_USER_WARNING
                    );
                    return false;
                }

                // replace the named argument with the corresponding numeric one
                $format = substr_replace(
                    $format, $replace = $arg_nums[$arg_key] . '$', $arg_pos, $arg_len
                );
                // skip to end of replacement for next iteration
                $pos = $arg_pos + strlen($replace);
    }

    return vsprintf($format, array_values($args));
}

/**
* Converts a phone number into the American International Standard
*
* @param $phone string with phone number
*
* @return string with phone number, not null
*/
function formatPhoneNumberToUSInternational($phone)
{
    if (preg_match('/^\+(\d{1})(\d{3})(\d{3})(\d{4})$/', $phone,  $matches)) {
        return "+" . $matches[1] . " " . $matches[2] . '-' .$matches[3] . '-' .
         $matches[4];
    }
    return $phone;
}

/**
* Set new enviroment values into some .env file
*
* @param $vars associative array with the new values to be set
* @param $filepath Path of the .env file
*
* @return string with the new content, if the file exists
*/
function updateEnv($vars, $filepath = '.env')
{
    $path = base_path($filepath);
    if (file_exists($path)) {
        $content = file_get_contents($path);
        foreach ($vars as $key => $value) {
            $content = addOrReplaceEnvVar($key, $value, $content);
        }
        file_put_contents($path, $content);
        return $content;
    }
}

/**
* Set new enviroment values into some .env file
*
* @param $varName Environment variable name
* @param $newVarValue Environment variable value
* @param $content Content where to replace variables
*
* @return string with the new content with the variables replaced
*/
function addOrReplaceEnvVar($varName, $newVarValue, $content)
{
    if (preg_match("/$varName=(.*)/", $content)) {
        return preg_replace("/$varName=(.*)/", "$varName=$newVarValue", $content);
    } else {
        return $content.PHP_EOL."$varName=$newVarValue".PHP_EOL;
    }
}
