<?php
/**
 * Popup template: Yes / No buttons.
 *
 * @var string $style           Popup style: light|dark.
 * @var string $overlay_color   Overlay background color.
 * @var string $title           Popup title.
 * @var string $description     Popup description.
 * @var string $button_yes_text Confirm button label.
 * @var string $button_no_text  Decline button label.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="avps-overlay" class="avps-overlay avps-style-<?php echo esc_attr( $style ); ?>" style="background: <?php echo esc_attr( $overlay_color ); ?>;">
    <div class="avps-popup">
        <h2 class="avps-title"><?php echo esc_html( $title ); ?></h2>
        <p class="avps-description"><?php echo esc_html( $description ); ?></p>
        <div class="avps-buttons">
            <button type="button" class="avps-btn avps-btn-yes" data-action="confirm">
                <?php echo esc_html( $button_yes_text ); ?>
            </button>
            <button type="button" class="avps-btn avps-btn-no" data-action="decline">
                <?php echo esc_html( $button_no_text ); ?>
            </button>
        </div>
    </div>
</div>
