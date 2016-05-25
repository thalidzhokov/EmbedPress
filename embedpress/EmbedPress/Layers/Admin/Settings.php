<?php
namespace EmbedPress\Layers\Admin;

(defined('ABSPATH') && defined('EMBEDPRESS_IS_LOADED')) or die("No direct script access allowed.");

class Settings
{
    private static $namespace = '\EmbedPress\Layers\Admin\Settings';
    private static $identifier = "plg_embedpress";
    private static $sectionAdminIdentifier = "embedpress_options_admin";
    private static $sectionGroupIdentifier = "embedpress_options";
    private static $fieldMap = array(
        'displayPreviewBox' => array(
            'label'   => "Display Preview Box inside editor",
            'section' => "admin"
        ),
        'disablePluginInAdmin' => array(
            'label'   => "Disable EmbedPress in the Admin area",
            'section' => "admin"
        )
    );

    public function __construct()
    {}

    public function __clone()
    {}

    public static function registerMenuItem()
    {
        add_options_page('EmbedPress Settings', 'EmbedPress', 'manage_options', 'embedpress', array(self::$namespace, 'renderForm'));
    }

    public static function registerActions()
    {
        register_setting(self::$sectionGroupIdentifier, self::$sectionGroupIdentifier, array(self::$namespace, "validateForm"));

        add_settings_section(self::$sectionAdminIdentifier, 'Admin Section Settings', array(self::$namespace, 'renderHelpText'), self::$identifier);

        foreach (self::$fieldMap as $fieldName => $field) {
            add_settings_field($fieldName, $field['label'], array(self::$namespace, "renderField_{$fieldName}"), self::$identifier, self::${"section". ucfirst($field['section']) ."Identifier"});
        }
    }

    public static function renderForm()
    {
        ?>
        <div>
            <h2>EmbedPress Settings Page</h2>
            <form action="options.php" method="POST">
                <?php settings_fields(self::$sectionGroupIdentifier); ?>
                <?php do_settings_sections(self::$identifier); ?>

                <input name="Submit" type="submit" value="Save changes" />
            </form>
        </div>
        <?php
    }

    public static function validateForm($freshData)
    {
        $data = array(
            'displayPreviewBox'    => (bool)$freshData['displayPreviewBox'],
            'disablePluginInAdmin' => (bool)$freshData['disablePluginInAdmin']
        );

        return $data;
    }

    public static function renderHelpText()
    {}

    public static function renderField_displayPreviewBox()
    {
        $fieldName = "displayPreviewBox";

        $options = get_option(self::$sectionGroupIdentifier);

        echo '<label><input type="radio" id="'. $fieldName .'_0" name="'. self::$sectionGroupIdentifier .'['. $fieldName .']" value="0" '. (!$options[$fieldName] ? "checked" : "") .' /> No</label>';
        echo "&nbsp;&nbsp;";
        echo '<label><input type="radio" id="'. $fieldName .'_1" name="'. self::$sectionGroupIdentifier .'['. $fieldName .']" value="1" '. ($options[$fieldName] ? "checked" : "") .' /> Yes</label>';
    }

    public static function renderField_disablePluginInAdmin()
    {
        $fieldName = "disablePluginInAdmin";

        $options = get_option(self::$sectionGroupIdentifier);

        $options[$fieldName] = !isset($options[$fieldName]) ? true : (bool)$options[$fieldName];

        echo '<label><input type="radio" id="'. $fieldName .'_1" name="'. self::$sectionGroupIdentifier .'['. $fieldName .']" value="1" '. ($options[$fieldName] ? "checked" : "") .' /> Disable</label>';
        echo "&nbsp;&nbsp;";
        echo '<label><input type="radio" id="'. $fieldName .'_0" name="'. self::$sectionGroupIdentifier .'['. $fieldName .']" value="0" '. (!$options[$fieldName] ? "checked" : "") .' /> Keep it enabled</label>';
    }
}