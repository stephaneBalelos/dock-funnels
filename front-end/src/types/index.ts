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
    required: boolean;
    validation?: string; // Regex or other validation rules
    custom_attributes?: Record<string, string>; // Additional attributes like data-* attributes
    css_classes?: string[]; // Custom CSS classes for styling
    error_message?: string;
    step_id: number; // ID of the step this field belongs to
}

export type FormFieldSelect = FormField & {
    type: 'select'; // Select dropdown type
    label?: string; // Label for the field
    options: { id: string; value: string; label: string, description?: string }[]; // Options for the select field
    multiple?: boolean; // Whether the select allows multiple selections
    default_value?: string | string[]; // Default value(s) for the field
}

export type FormFieldText = FormField & {
    type: 'text' // Input types
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

