        <tr valign="top" id="toggle-fields">
			<th>
				<?php esc_attr_e( 'Additional Email Fields', 'bnfw' ); ?>
			</th>
			<td>
				<input type="checkbox" id="show-fields" name="show-fields" value="true" <?php checked( $setting['show-fields'], 'true', true ); ?>>
				<?php esc_html_e( 'Show additional email fields', 'bnfw' ); ?>
			</td>
        </tr>

        <tr valign="top" id="email">
            <th scope="row">
                <?php _e( 'From Name and Email', 'bnfw' ); ?>
            </th>
            <td>
            <input type="text" name="from-name" value="<?php echo $setting['from-name']; ?>" placeholder="Site Name" style="width:37%">
                <input type="email" name="from-email" value="<?php echo $setting['from-email']; ?>" placeholder="Admin Email" style="width:37%">
            </td>
        </tr>

        <tr valign="top" id="cc">
            <th scope="row">
                <?php _e( 'CC', 'bnfw' ); ?>
            </th>

            <td>
                <?php $this->render_roles_dropdown( 'cc-roles', $setting['cc-roles'] ); ?>
                <input type="email" name="cc-email" value="<?php echo $setting['cc-email']; ?>" placeholder="Additional email addresses" style="width:50%;">
            </td>
        </tr>

        <tr valign="top" id="bcc">
            <th scope="row">
                <?php _e( 'BCC', 'bnfw' ); ?>
            </th>

            <td>
                <?php $this->render_roles_dropdown( 'bcc-roles', $setting['bcc-roles'] ); ?>
                <input type="email" name="bcc-email" value="<?php echo $setting['bcc-email']; ?>" placeholder="Additional email addresses" style="width:50%;">
            </td>
        </tr>


