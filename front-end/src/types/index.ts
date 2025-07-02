// Editor State Types, this is the state of the form editor in the dashboard
export type FormState = {
    id?: number; // ID of the form, optional for new forms
    title: string; // Title of the form
    description: string; // Description of the form
    form_steps: FormStep[]; // Array of steps in the form
    form_fields: (FormFieldSelect | FormFieldText | FormFieldTextarea | FormFieldCheckboxList | FormFieldSubmissionSummary)[]; // Array of fields in this step
    form_settings: FormSettings; // Settings for the form
}

// Form is form data that is used to submit the form
export type Form = Omit<FormState, 'form_settings'>

// Form Settings Types, these are the settings for the form
export type FormSettings = {
    smtp_settings: FormSmtpSettings; // SMTP settings for sending emails
    notifications_settings: FormNotificationSettings
    email_settings: FormEmailSettings; // Settings to notify users via email when a form is submitted
    onSubmitAction: FormOnSubmitAction[]; // Actions to perform when the form is submitted
}

// Form On Submit Action Types, these are the actions that can be performed when the form is submitted
export type FormOnSubmitAction = FormOnSubmitActionRedirect | FormOnSubmitActionMail;

export type FormOnSubmitActionRedirect = {
    type: 'redirect'; // Type of action is redirect
    url: string; // URL to redirect to after form submission
    open_in_new_tab: boolean; // Whether to open the URL in a new tab
}

export type FormOnSubmitActionMail = {
    type: 'mail'; // Type of action is mail
    subject: string; // Subject of the email to send
    body: string; // Body of the email to send
    email_field: string; // Name of the field that contains the user's email address
}

// SMTP settings for sending emails
export type FormSmtpSettings = {
    enabled: boolean; // Whether SMTP is enabled
    host: string; // SMTP server host
    port: number; // SMTP server port
    username: string; // SMTP username
    password: string; // SMTP password
    encryption: 'none' | 'ssl' | 'tls'; // Encryption type
    from_name: string; // Name of the sender
    from_email: string; // Email address of the sender
    reply_to: string; // Reply-to email address
}

// Settings to notify Form Admins via email when a form is submitted
export type FormNotificationSettings = {
    emails: string; // Email addresses to notify, comma-separated
    subject: string; // Subject of the notification email
    body: string; // Message body of the notification email
}

// Settings to notify users via email when a form is submitted
export type FormEmailSettings = {
    enabled: boolean; // Whether email notifications are enabled
    send_to_admin: boolean; // Whether to send notifications to the admin
    send_to_user: boolean; // Whether to send notifications to the user
    user_email_field: string; // Name of the field that contains the user's email address
    subject: string; // Subject of the notification email
    body: string; // Message body of the notification email
}

export type FormStep = {
    title: string;
    description: string;
}

export type FormField = {
    field_name: string; // Name of the field, used for form submission unique identification
    label: string;
    description?: string; // Description of the field
    step_index: number; // Index of the step this field belongs to defaults to 0
    required: boolean;
    custom_attributes?: Record<string, string>; // Additional attributes like data-* attributes
    field_settings?: Record<string, any>; // Additional settings for the field
    css_classes?: string[]; // Custom CSS classes for styling
    error_message?: string;
    depends_on?: FormFieldDependsOn[]; // Conditional logic for showing/hiding the field
}

export type FormFieldDependsOn = {
    field_name: string; // Name of the field this field depends on
    value: string; // Value(s) that trigger this field to be shown
}

export type FormFieldSelect = FormField & {
    type: 'select'; // Select dropdown type
    options: FormFieldSelectOption[]; // Array of options for the select field
    multiple?: boolean; // Whether the select allows multiple selections
    default_value?: string | string[]; // Default value(s) for the field
    validation?: string; // Regex or other validation rules
}

export type FormFieldSelectOption = {
    value: string; // Value of the option
    label: string; // Display label for the option
    description?: string; // Optional description for the option
    depends_on: FormFieldDependsOn[]; // Conditional logic for showing/hiding the option
}

export type FormFieldText = FormField & {
    type: 'text'; // Input types
    input_type?: 'text' | 'email' | 'number' | 'tel' | 'url' | 'date'; // Type of text input field
    placeholder?: string; // Placeholder text
    default_value?: string; // Default value for the field
}
export type FormFieldTextarea = FormField & {
    type: 'textarea'; // Textarea type
    placeholder?: string; // Placeholder text
    default_value?: string; // Default value for the field
    rows?: number; // Number of rows for the textarea
    cols?: number; // Number of columns for the textarea
}

export type FormFieldCheckboxList = FormField & {
    type: 'checkboxList'; // Checkbox type
    default_value?: string[]; // Default value(s) for the checkbox
    options: FormFieldCheckboxListOption[]; // Array of options for the checkbox list
    min?: number; // Minimum number of checkboxes that must be selected
    max?: number; // Maximum number of checkboxes that can be selected
}

export type FormFieldCheckboxListOption = {
    value: string; // Value of the option
    label: string; // Display label for the option
    description?: string; // Optional description for the option
    depends_on: FormFieldDependsOn[]; // Conditional logic for showing/hiding the option
}

export type FormFieldSubmissionSummary = FormField & {
    required: false; // Submission summary fields are not required
    show_full_summary?: boolean; // Whether to show the full summary for this field
    default_value?: null; // Default value for the field, usually null
    type: 'submissionSummary'; // Type of the field
}


export type FormSubmission = {
    form_id: number; // ID of the form being submitted
    fields: Record<string, FormSubmissionField>; // Keyed by field_id, array of values submitted for each field
    timestamp?: string; // ISO 8601 timestamp of the submission
}

export type FormSubmissionField = {
    field_name: FormField['field_name']; // Name of the field, used for form submission
    value: string | string[] | null; // Value(s) submitted for the field
    step_index: number
}