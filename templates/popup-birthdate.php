<?php
/**
 * Popup template: date of birth input.
 *
 * @var string $style         Popup style: light|dark.
 * @var string $overlay_color Overlay background color.
 * @var string $title         Popup title.
 * @var int    $minimum_age   Minimum age required.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="avps-overlay" class="avps-overlay avps-style-<?php echo esc_attr( $style ); ?>" style="background: <?php echo esc_attr( $overlay_color ); ?>;">
    <div class="avps-popup">
        <h2 class="avps-title"><?php echo esc_html( $title ); ?></h2>
        <p class="avps-description">
            <?php
            printf(
                /* translators: %d: minimum age */
                esc_html__( 'You must be %d+ to enter this site. Please enter your date of birth.', 'pavel-silinskii-age-verification' ),
                (int) $minimum_age
            );
            ?>
        </p>
        <div class="avps-birthdate-form">
            <input type="date" id="avps-birthdate" name="birthdate"
                   max="<?php echo esc_attr( current_time( 'Y-m-d' ) ); ?>"
                   placeholder="YYYY-MM-DD">
            <p class="avps-error" id="avps-error" style="display:none;"></p>
            <button type="button" class="avps-btn avps-btn-yes" id="avps-submit">
                <?php esc_html_e( 'Enter Site', 'pavel-silinskii-age-verification' ); ?>
            </button>
        </div>
    </div>
</div>
