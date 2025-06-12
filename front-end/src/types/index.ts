export type Form = {
    id: number;
    title: string;
    description: string;
    form_steps: FormStep[];
}

export type FormStep = {
    id: number;
    title: string;
    description: string;
    order: number;
    fields: (FormFieldSelect | FormFieldText | FormFieldTextarea)[]; // Array of fields in this step
}
export type FormField = {
    id: number;
    name: string;
    description?: string; // Description of the field
    field_name: string; // Name of the field, used for form submission
    required: boolean;
    validation?: string; // Regex or other validation rules
    custom_attributes?: Record<string, string>; // Additional attributes like data-* attributes
    css_classes?: string[]; // Custom CSS classes for styling
    error_message?: string;
    step_id: number; // ID of the step this field belongs to
    depends_on?: {
        field_id: number; // ID of the field this field depends on
        value: string | string[]; // Value(s) that trigger this field to be shown
    }; // Conditional logic for showing/hiding the field
}

export type FormFieldSelect = FormField & {
    type: 'select'; // Select dropdown type
    label?: string; // Label for the field
    options: FormFieldSelectOptions[]; // Array of options for the select field
    multiple?: boolean; // Whether the select allows multiple selections
    default_value?: string | string[]; // Default value(s) for the field
}

export type FormFieldSelectOptions = {
    id: string; // Unique identifier for the option
    value: string; // Value of the option
    label: string; // Display label for the option
    description?: string; // Optional description for the option
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


export type FormSubmission = {
    form_id: number; // ID of the form being submitted
    fields: Record<string, FormSubmissionField>; // Keyed by field_id, array of values submitted for each field
    timestamp?: string; // ISO 8601 timestamp of the submission
}

export type FormSubmissionField = {
    field_id: number; // ID of the field
    field_label: string; // Label of the field
    field_name: string; // Name of the field
    value: string | string[] | null; // Value(s) submitted for the field
    step_id: number; // ID of the step this field belongs to
    step_title: string; // Title of the step this field belongs to
    step_order: number; // Order of the step this field belongs to
}