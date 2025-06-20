export type Form = {
    id: number;
    title: string;
    description: string;
    form_steps: FormStep[];
    fields: (FormFieldSelect | FormFieldText | FormFieldTextarea | FormFieldCheckboxList)[]; // Array of fields in this step
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
    depends_on?: {
        field_name: string; // name of the field this option depends on
        value: string; // Value(s) that trigger this option to be shown
    }[]; // Conditional logic for showing/hiding the option
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
    depends_on?: {
        field_name: string; // name of the field this option depends on
        value: string; // Value(s) that trigger this option to be shown
    }[]; // Conditional logic for showing/hiding the option
}


export type FormSubmission = {
    form_id: number; // ID of the form being submitted
    fields: Record<string, FormSubmissionField>; // Keyed by field_id, array of values submitted for each field
    timestamp?: string; // ISO 8601 timestamp of the submission
}

export type FormSubmissionField = {
    field_label: FormField['label']; // Label of the field
    field_name: FormField['field_name']; // Name of the field, used for form submission
    value: string | string[] | null; // Value(s) submitted for the field
    step_index: number
    step_title: FormStep['title']; // Title of the step this field belongs to
}