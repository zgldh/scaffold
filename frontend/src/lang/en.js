/**
 * Created by zhangwb-pc on 03/28/2018.
 */

export default {
  app_name: 'Backend Manager',
  sign_in: 'Sign In',
  password: {
    send: 'send',
    reset_password: 'reset password',
    send_email_success: 'Reset password mail sent successfully! Please check the email to reset the password'
  },
  navbar: {
    my_profile: 'My Profile',
    log_out: 'Log Out',
    dashboard: 'Dashboard',
    github: 'Github',
    screenfull: 'screenfull',
    language: 'Language',
    notification: 'Notification',
    theme: 'theme'
  },
  routes: {
    dashboard: 'Dashboard',
    my_profile: 'My Profile',
    role_list: 'Role List',
    role_create: 'Create Role',
    role_edit: 'Edit Role'
  },
  list: {
    advance_search: 'Advance search',
    export_button: 'Export to CSV',
    export_file_name: 'Export-{title}-{timestamp}'
  },
  pages: {
    role: {
      terms: {
        edit_permission: 'Edit Permission',
        copy_complete_text: 'Copy complete. Do you want to edit the new role?'
      }
    },
    my_profile: {
      basic: 'Basic Information',
      change_password: 'Change Password',
      old_password: 'Old Password',
      new_password: 'New Password',
      repeat: 'Repeat'
    }
  },
  components: {
    avatar_editor: {
      button_text: 'Change Avatar',
      title: 'Avatar'
    },
    advance_search: {
      add_button: 'Add a filter'
    },
    editor_title: {
      create: 'Create {name}',
      edit: 'Edit {name}'
    },
    list_title: '{name} List',
    lang_select: {
      complete_text: 'Switch language complete'
    },
    permission_matrix: {
      model_row: {
        other: 'Other'
      }
    },
    permission_editor_dialog: {
      warning: 'DO NOT MODIFY THE "NAME", if you do not get it!'
    },
    notification: {
      latest: 'Latest',
      mark_all_as_read: 'Mark all as read',
      mark_as_read: 'Mark as read',
      mark_as_unread: 'Mark as unread',
      no_notification: 'Great! No notification!',
      no_more: 'There is no more notifications'
    }
  },
  messages: {
    role_copy: {
      title: 'Copy the role {label}',
      text: 'Please input new role name',
      input_error_message: 'Start with alphabet, accessible characters: a-zA-Z0-9-_'
    },
    text_copy: {
      complete: 'Copy complete',
      title: 'Copy failed',
      text: 'Please copy following text manually'
    },
    session_expired: {
      title: 'Session is expired',
      text: 'Your session is expired. Click Cancel to stay on this page or Re-Login.',
      confirm: 'Re-Login',
      cancel: 'Cancel'
    },
    delete_confirm: {
      title: 'Delete Confirm',
      text: 'Are you sure to delete {name}?',
      confirm: 'Delete',
      cancel: 'Cancel',
      success_text: '{name} has been deleted.',
      cancel_text: 'Canceled'
    }
  },
  validator: {}
}
