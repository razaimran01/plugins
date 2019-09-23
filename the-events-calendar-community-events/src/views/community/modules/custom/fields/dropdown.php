<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * Event Submission Form Custom Select field.
 * This is used to add a custom select field to the event submission form that contains custom field inputs.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/custom/fields/dropdown.php
 *
 * @since   4.6.3
 * @version 4.6.3
 *
 * @var array        $fields        List of form fields.
 * @var int          $post_id       Current Post ID.
 * @var array        $field         Current field data.
 * @var string       $field_classes List of field classes separated by space.
 * @var string       $field_name    Field name.
 * @var string       $field_label   Field label.
 * @var string       $field_type    Field type.
 * @var string       $field_id      Field HTML ID.
 * @var string|array $value         Current field value (or value from $_POST).
 * @var array        $options       List of options for checkbox/radio/dropdown fields.
 */
?>

<select
	class="tribe-dropdown"
	id="<?php echo esc_attr( $field_id ); ?>"
	name="<?php echo esc_attr( $field_name ); ?>"
>
	<?php foreach ( $options as $option ) : ?>
		<option
			value="<?php echo esc_attr( $option ); ?>"
			<?php selected( in_array( $option, (array) $value, true ) ); ?>
		>
			<?php echo esc_html( $option ); ?>
		</option>
	<?php endforeach; ?>
</select>
