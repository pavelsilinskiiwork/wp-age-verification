<?php
/**
 * Settings page markup.
 *
 * @var array $s Current settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$avps_pages      = get_pages();
$avps_categories = get_categories( [ 'hide_empty' => false ] );
?>
<div class="wrap avps-admin-wrap">
    <h1><?php esc_html_e( 'Age Verification', 'pavel-silinskii-age-verification' ); ?></h1>

    <div id="avps-notice" class="notice" style="display:none;"><p></p></div>

    <form id="avps-settings-form">

        <h2 class="title"><?php esc_html_e( 'General', 'pavel-silinskii-age-verification' ); ?></h2>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><?php esc_html_e( 'Enable plugin', 'pavel-silinskii-age-verification' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="enabled" value="1" <?php checked( ! empty( $s['enabled'] ) ); ?>>
                        <?php esc_html_e( 'Show the age verification popup on the site', 'pavel-silinskii-age-verification' ); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="avps-minimum-age"><?php esc_html_e( 'Minimum age', 'pavel-silinskii-age-verification' ); ?></label></th>
                <td>
                    <select id="avps-minimum-age" name="minimum_age">
                        <?php foreach ( [ 14, 16, 18, 21 ] as $avps_age ) : ?>
                            <option value="<?php echo esc_attr( $avps_age ); ?>" <?php selected( (int) $s['minimum_age'], $avps_age ); ?>><?php echo esc_html( $avps_age ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Verification type', 'pavel-silinskii-age-verification' ); ?></th>
                <td>
                    <label><input type="radio" name="verification_type" value="buttons" <?php checked( $s['verification_type'], 'buttons' ); ?>> <?php esc_html_e( 'Buttons (Yes / No)', 'pavel-silinskii-age-verification' ); ?></label><br>
                    <label><input type="radio" name="verification_type" value="birthdate" <?php checked( $s['verification_type'], 'birthdate' ); ?>> <?php esc_html_e( 'Date of Birth', 'pavel-silinskii-age-verification' ); ?></label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="avps-cookie-duration"><?php esc_html_e( 'Cookie duration (days)', 'pavel-silinskii-age-verification' ); ?></label></th>
                <td><input type="number" min="1" id="avps-cookie-duration" name="cookie_duration" value="<?php echo esc_attr( $s['cookie_duration'] ); ?>" class="small-text"></td>
            </tr>
        </table>

        <h2 class="title"><?php esc_html_e( 'Content Scope', 'pavel-silinskii-age-verification' ); ?></h2>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><?php esc_html_e( 'Scope', 'pavel-silinskii-age-verification' ); ?></th>
                <td>
                    <label><input type="radio" name="scope" value="entire_site" <?php checked( $s['scope'], 'entire_site' ); ?>> <?php esc_html_e( 'Entire site', 'pavel-silinskii-age-verification' ); ?></label><br>
                    <label><input type="radio" name="scope" value="specific" <?php checked( $s['scope'], 'specific' ); ?>> <?php esc_html_e( 'Specific pages &amp; categories', 'pavel-silinskii-age-verification' ); ?></label>
                </td>
            </tr>
            <tr class="avps-scope-specific">
                <th scope="row"><label for="avps-specific-pages"><?php esc_html_e( 'Specific pages', 'pavel-silinskii-age-verification' ); ?></label></th>
                <td>
                    <select multiple size="6" id="avps-specific-pages" name="specific_pages[]" class="regular-text">
                        <?php foreach ( $avps_pages as $avps_page ) : ?>
                            <option value="<?php echo esc_attr( $avps_page->ID ); ?>" <?php selected( in_array( (int) $avps_page->ID, array_map( 'intval', (array) $s['specific_pages'] ), true ) ); ?>>
                                <?php echo esc_html( $avps_page->post_title ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr class="avps-scope-specific">
                <th scope="row"><label for="avps-specific-categories"><?php esc_html_e( 'Specific categories', 'pavel-silinskii-age-verification' ); ?></label></th>
                <td>
                    <select multiple size="6" id="avps-specific-categories" name="specific_categories[]" class="regular-text">
                        <?php foreach ( $avps_categories as $avps_cat ) : ?>
                            <option value="<?php echo esc_attr( $avps_cat->term_id ); ?>" <?php selected( in_array( (int) $avps_cat->term_id, array_map( 'intval', (array) $s['specific_categories'] ), true ) ); ?>>
                                <?php echo esc_html( $avps_cat->name ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>

        <h2 class="title"><?php esc_html_e( 'Popup Text', 'pavel-silinskii-age-verification' ); ?></h2>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><label for="avps-popup-title"><?php esc_html_e( 'Popup title', 'pavel-silinskii-age-verification' ); ?></label></th>
                <td><input type="text" id="avps-popup-title" name="popup_title" value="<?php echo esc_attr( $s['popup_title'] ); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="avps-popup-description"><?php esc_html_e( 'Popup description', 'pavel-silinskii-age-verification' ); ?></label></th>
                <td><textarea id="avps-popup-description" name="popup_description" rows="3" class="large-text"><?php echo esc_textarea( $s['popup_description'] ); ?></textarea></td>
            </tr>
            <tr class="avps-type-buttons">
                <th scope="row"><label for="avps-button-yes"><?php esc_html_e( 'Button YES text', 'pavel-silinskii-age-verification' ); ?></label></th>
                <td><input type="text" id="avps-button-yes" name="button_yes_text" value="<?php echo esc_attr( $s['button_yes_text'] ); ?>" class="regular-text"></td>
            </tr>
            <tr class="avps-type-buttons">
                <th scope="row"><label for="avps-button-no"><?php esc_html_e( 'Button NO text', 'pavel-silinskii-age-verification' ); ?></label></th>
                <td><input type="text" id="avps-button-no" name="button_no_text" value="<?php echo esc_attr( $s['button_no_text'] ); ?>" class="regular-text"></td>
            </tr>
        </table>

        <h2 class="title"><?php esc_html_e( 'Popup Style', 'pavel-silinskii-age-verification' ); ?></h2>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><?php esc_html_e( 'Style', 'pavel-silinskii-age-verification' ); ?></th>
                <td>
                    <label><input type="radio" name="popup_style" value="light" <?php checked( $s['popup_style'], 'light' ); ?>> <?php esc_html_e( 'Light', 'pavel-silinskii-age-verification' ); ?></label><br>
                    <label><input type="radio" name="popup_style" value="dark" <?php checked( $s['popup_style'], 'dark' ); ?>> <?php esc_html_e( 'Dark', 'pavel-silinskii-age-verification' ); ?></label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="avps-overlay-color"><?php esc_html_e( 'Overlay color', 'pavel-silinskii-age-verification' ); ?></label></th>
                <td><input type="text" id="avps-overlay-color" name="overlay_color" value="<?php echo esc_attr( $s['overlay_color'] ); ?>" class="regular-text" placeholder="rgba(0,0,0,0.85)"></td>
            </tr>
        </table>

        <h2 class="title"><?php esc_html_e( 'Decline Action', 'pavel-silinskii-age-verification' ); ?></h2>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><?php esc_html_e( 'Action', 'pavel-silinskii-age-verification' ); ?></th>
                <td>
                    <label><input type="radio" name="decline_action" value="block" <?php checked( $s['decline_action'], 'block' ); ?>> <?php esc_html_e( 'Block page', 'pavel-silinskii-age-verification' ); ?></label><br>
                    <label><input type="radio" name="decline_action" value="redirect" <?php checked( $s['decline_action'], 'redirect' ); ?>> <?php esc_html_e( 'Redirect', 'pavel-silinskii-age-verification' ); ?></label>
                </td>
            </tr>
            <tr class="avps-decline-redirect">
                <th scope="row"><label for="avps-redirect-url"><?php esc_html_e( 'Redirect URL', 'pavel-silinskii-age-verification' ); ?></label></th>
                <td><input type="url" id="avps-redirect-url" name="redirect_url" value="<?php echo esc_attr( $s['redirect_url'] ); ?>" class="regular-text"></td>
            </tr>
            <tr class="avps-decline-block">
                <th scope="row"><label for="avps-blocked-message"><?php esc_html_e( 'Blocked message', 'pavel-silinskii-age-verification' ); ?></label></th>
                <td><textarea id="avps-blocked-message" name="blocked_message" rows="3" class="large-text"><?php echo esc_textarea( $s['blocked_message'] ); ?></textarea></td>
            </tr>
        </table>

        <p class="submit">
            <button type="submit" class="button button-primary" id="avps-save"><?php esc_html_e( 'Save Settings', 'pavel-silinskii-age-verification' ); ?></button>
            <span class="spinner" id="avps-spinner"></span>
        </p>
    </form>
</div>
