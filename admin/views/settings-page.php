<?php
/**
 * Settings page markup.
 *
 * @var array $s Current settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$pages      = get_pages();
$categories = get_categories( [ 'hide_empty' => false ] );
?>
<div class="wrap wav-admin-wrap">
    <h1><?php esc_html_e( 'Age Verification', 'wp-age-verification' ); ?></h1>

    <div id="wav-notice" class="notice" style="display:none;"><p></p></div>

    <form id="wav-settings-form">

        <h2 class="title"><?php esc_html_e( 'General', 'wp-age-verification' ); ?></h2>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><?php esc_html_e( 'Enable plugin', 'wp-age-verification' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="enabled" value="1" <?php checked( ! empty( $s['enabled'] ) ); ?>>
                        <?php esc_html_e( 'Show the age verification popup on the site', 'wp-age-verification' ); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wav-minimum-age"><?php esc_html_e( 'Minimum age', 'wp-age-verification' ); ?></label></th>
                <td>
                    <select id="wav-minimum-age" name="minimum_age">
                        <?php foreach ( [ 14, 16, 18, 21 ] as $age ) : ?>
                            <option value="<?php echo esc_attr( $age ); ?>" <?php selected( (int) $s['minimum_age'], $age ); ?>><?php echo esc_html( $age ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Verification type', 'wp-age-verification' ); ?></th>
                <td>
                    <label><input type="radio" name="verification_type" value="buttons" <?php checked( $s['verification_type'], 'buttons' ); ?>> <?php esc_html_e( 'Buttons (Yes / No)', 'wp-age-verification' ); ?></label><br>
                    <label><input type="radio" name="verification_type" value="birthdate" <?php checked( $s['verification_type'], 'birthdate' ); ?>> <?php esc_html_e( 'Date of Birth', 'wp-age-verification' ); ?></label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wav-cookie-duration"><?php esc_html_e( 'Cookie duration (days)', 'wp-age-verification' ); ?></label></th>
                <td><input type="number" min="1" id="wav-cookie-duration" name="cookie_duration" value="<?php echo esc_attr( $s['cookie_duration'] ); ?>" class="small-text"></td>
            </tr>
        </table>

        <h2 class="title"><?php esc_html_e( 'Content Scope', 'wp-age-verification' ); ?></h2>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><?php esc_html_e( 'Scope', 'wp-age-verification' ); ?></th>
                <td>
                    <label><input type="radio" name="scope" value="entire_site" <?php checked( $s['scope'], 'entire_site' ); ?>> <?php esc_html_e( 'Entire site', 'wp-age-verification' ); ?></label><br>
                    <label><input type="radio" name="scope" value="specific" <?php checked( $s['scope'], 'specific' ); ?>> <?php esc_html_e( 'Specific pages &amp; categories', 'wp-age-verification' ); ?></label>
                </td>
            </tr>
            <tr class="wav-scope-specific">
                <th scope="row"><label for="wav-specific-pages"><?php esc_html_e( 'Specific pages', 'wp-age-verification' ); ?></label></th>
                <td>
                    <select multiple size="6" id="wav-specific-pages" name="specific_pages[]" class="regular-text">
                        <?php foreach ( $pages as $page ) : ?>
                            <option value="<?php echo esc_attr( $page->ID ); ?>" <?php selected( in_array( (int) $page->ID, array_map( 'intval', (array) $s['specific_pages'] ), true ) ); ?>>
                                <?php echo esc_html( $page->post_title ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr class="wav-scope-specific">
                <th scope="row"><label for="wav-specific-categories"><?php esc_html_e( 'Specific categories', 'wp-age-verification' ); ?></label></th>
                <td>
                    <select multiple size="6" id="wav-specific-categories" name="specific_categories[]" class="regular-text">
                        <?php foreach ( $categories as $cat ) : ?>
                            <option value="<?php echo esc_attr( $cat->term_id ); ?>" <?php selected( in_array( (int) $cat->term_id, array_map( 'intval', (array) $s['specific_categories'] ), true ) ); ?>>
                                <?php echo esc_html( $cat->name ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>

        <h2 class="title"><?php esc_html_e( 'Popup Text', 'wp-age-verification' ); ?></h2>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><label for="wav-popup-title"><?php esc_html_e( 'Popup title', 'wp-age-verification' ); ?></label></th>
                <td><input type="text" id="wav-popup-title" name="popup_title" value="<?php echo esc_attr( $s['popup_title'] ); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="wav-popup-description"><?php esc_html_e( 'Popup description', 'wp-age-verification' ); ?></label></th>
                <td><textarea id="wav-popup-description" name="popup_description" rows="3" class="large-text"><?php echo esc_textarea( $s['popup_description'] ); ?></textarea></td>
            </tr>
            <tr class="wav-type-buttons">
                <th scope="row"><label for="wav-button-yes"><?php esc_html_e( 'Button YES text', 'wp-age-verification' ); ?></label></th>
                <td><input type="text" id="wav-button-yes" name="button_yes_text" value="<?php echo esc_attr( $s['button_yes_text'] ); ?>" class="regular-text"></td>
            </tr>
            <tr class="wav-type-buttons">
                <th scope="row"><label for="wav-button-no"><?php esc_html_e( 'Button NO text', 'wp-age-verification' ); ?></label></th>
                <td><input type="text" id="wav-button-no" name="button_no_text" value="<?php echo esc_attr( $s['button_no_text'] ); ?>" class="regular-text"></td>
            </tr>
        </table>

        <h2 class="title"><?php esc_html_e( 'Popup Style', 'wp-age-verification' ); ?></h2>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><?php esc_html_e( 'Style', 'wp-age-verification' ); ?></th>
                <td>
                    <label><input type="radio" name="popup_style" value="light" <?php checked( $s['popup_style'], 'light' ); ?>> <?php esc_html_e( 'Light', 'wp-age-verification' ); ?></label><br>
                    <label><input type="radio" name="popup_style" value="dark" <?php checked( $s['popup_style'], 'dark' ); ?>> <?php esc_html_e( 'Dark', 'wp-age-verification' ); ?></label>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wav-overlay-color"><?php esc_html_e( 'Overlay color', 'wp-age-verification' ); ?></label></th>
                <td><input type="text" id="wav-overlay-color" name="overlay_color" value="<?php echo esc_attr( $s['overlay_color'] ); ?>" class="regular-text" placeholder="rgba(0,0,0,0.85)"></td>
            </tr>
        </table>

        <h2 class="title"><?php esc_html_e( 'Decline Action', 'wp-age-verification' ); ?></h2>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><?php esc_html_e( 'Action', 'wp-age-verification' ); ?></th>
                <td>
                    <label><input type="radio" name="decline_action" value="block" <?php checked( $s['decline_action'], 'block' ); ?>> <?php esc_html_e( 'Block page', 'wp-age-verification' ); ?></label><br>
                    <label><input type="radio" name="decline_action" value="redirect" <?php checked( $s['decline_action'], 'redirect' ); ?>> <?php esc_html_e( 'Redirect', 'wp-age-verification' ); ?></label>
                </td>
            </tr>
            <tr class="wav-decline-redirect">
                <th scope="row"><label for="wav-redirect-url"><?php esc_html_e( 'Redirect URL', 'wp-age-verification' ); ?></label></th>
                <td><input type="url" id="wav-redirect-url" name="redirect_url" value="<?php echo esc_attr( $s['redirect_url'] ); ?>" class="regular-text"></td>
            </tr>
            <tr class="wav-decline-block">
                <th scope="row"><label for="wav-blocked-message"><?php esc_html_e( 'Blocked message', 'wp-age-verification' ); ?></label></th>
                <td><textarea id="wav-blocked-message" name="blocked_message" rows="3" class="large-text"><?php echo esc_textarea( $s['blocked_message'] ); ?></textarea></td>
            </tr>
        </table>

        <p class="submit">
            <button type="submit" class="button button-primary" id="wav-save"><?php esc_html_e( 'Save Settings', 'wp-age-verification' ); ?></button>
            <span class="spinner" id="wav-spinner"></span>
        </p>
    </form>
</div>
