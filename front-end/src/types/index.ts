export type Form = {
    id: number;
    title: string;
    description: string;
    form_steps: FormStep[];
    fields: (FormFieldSelect | FormFieldText | FormFieldTextarea | FormFieldCheckboxList)[]; // Array of fields in this step
}

export type FormStep = {
    id: number;
    title: string;
    description: string;
}

export type FormField = {
    id: number;
    step_index: number; // ID of the step this field belongs to
    label: string;
    description?: string; // Description of the field
    field_name: string; // Name of the field, used for form submission
    required: boolean;
    validation?: string; // Regex or other validation rules
    custom_attributes?: Record<string, string>; // Additional attributes like data-* attributes
    css_classes?: string[]; // Custom CSS classes for styling
    error_message?: string;
    depends_on?: {
        field_name: string; // name of the field this field depends on
        value: string | string[]; // Value(s) that trigger this field to be shown
    }; // Conditional logic for showing/hiding the field
}

export type FormFieldSelect = FormField & {
    type: 'select'; // Select dropdown type
    label?: string; // Label for the field
    options: FormFieldSelectOption[]; // Array of options for the select field
    multiple?: boolean; // Whether the select allows multiple selections
    default_value?: string | string[]; // Default value(s) for the field
}

export type FormFieldSelectOption = {
    value: string; // Value of the option
    label: string; // Display label for the option
    description?: string; // Optional description for the option
    depends_on?: {
        field_name: string; // name of the field this option depends on
        value: string | string[]; // Value(s) that trigger this option to be shown
    }; // Conditional logic for showing/hiding the option
}

export type FormFieldText = FormField & {
    type: 'text'; // Input types
    input_type?: 'text' | 'email' | 'number' | 'tel' | 'url'; // Type of input field
    label?: string; // Label for the field
    placeholder?: string; // Placeholder text
    default_value?: string; // Default value for the field
}
export type FormFieldTextarea = FormField & {
    type: 'textarea'; // Textarea type
    label?: string; // Label for the field
    placeholder?: string; // Placeholder text
    default_value?: string; // Default value for the field
    rows?: number; // Number of rows for the textarea
    cols?: number; // Number of columns for the textarea
}

export type FormFieldCheckboxList = FormField & {
    type: 'checkboxList'; // Checkbox type
    label?: string; // Label for the checkbox
    description?: string; // Description of the checkbox
    default_value?: string[]; // Default value(s) for the checkbox
    options: FormFieldCheckboxListOption[]; // Array of options for the checkbox list
}

export type FormFieldCheckboxListOption = {
    value: string; // Value of the option
    label: string; // Display label for the option
    description?: string; // Optional description for the option
    depends_on?: {
        field_name: string; // name of the field this option depends on
        value: string | string[]; // Value(s) that trigger this option to be shown
    }; // Conditional logic for showing/hiding the option
}


export type FormSubmission = {
    form_id: number; // ID of the form being submitted
    fields: Record<string, FormSubmissionField>; // Keyed by field_id, array of values submitted for each field
    timestamp?: string; // ISO 8601 timestamp of the submission
}

export type FormSubmissionField = {
    field_id: FormField['id']; // ID of the field being submitted
    field_label: FormField['label']; // Label of the field
    field_name: FormField['field_name']; // Name of the field, used for form submission
    value: string | string[] | null; // Value(s) submitted for the field
    step_id: FormStep['id']; // ID of the step this field belongs to
    step_title: FormStep['title']; // Title of the step this field belongs to
}