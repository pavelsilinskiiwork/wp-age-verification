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
<div id="wav-overlay" class="wav-overlay wav-style-<?php echo esc_attr( $style ); ?>" style="background: <?php echo esc_attr( $overlay_color ); ?>;">
    <div class="wav-popup">
        <h2 class="wav-title"><?php echo esc_html( $title ); ?></h2>
        <p class="wav-description">
            <?php
            printf(
                /* translators: %d: minimum age */
                esc_html__( 'You must be %d+ to enter this site. Please enter your date of birth.', 'wp-age-verification' ),
                (int) $minimum_age
            );
            ?>
        </p>
        <div class="wav-birthdate-form">
            <input type="date" id="wav-birthdate" name="birthdate"
                   max="<?php echo esc_attr( date( 'Y-m-d' ) ); ?>"
                   placeholder="YYYY-MM-DD">
            <p class="wav-error" id="wav-error" style="display:none;"></p>
            <button type="button" class="wav-btn wav-btn-yes" id="wav-submit">
                <?php esc_html_e( 'Enter Site', 'wp-age-verification' ); ?>
            </button>
        </div>
    </div>
</div>
