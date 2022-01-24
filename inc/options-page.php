<div>

  <?php screen_icon(); ?>

  <h2>WP Banner Rotator</h2>

  <form method="post" action="options.php">

    <?php settings_fields( 'wpbr_options_group' ); ?>

    <table>

      <tr valign="top">

        <th scope="row">

          <label for="wpbr_option_name">Label</label>

        </th>

        <td>

          <input type="text" id="wpbr_option_name" name="wpbr_option_name" value="<?php echo get_option('wpbr_option_name'); ?>" />

        </td>

      </tr>

    </table>

    <?php  submit_button(); ?>

  </form>

</div>
