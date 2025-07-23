export const createForm = async (endpoint: string, nonce: string, formState: string) => {
    try {
        const response = await fetch(`${endpoint}?action=dock_funnel_ajax_create_form`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-WP-Nonce': nonce,
            },
            body: JSON.stringify({
                form_state: formState,
                nonce: nonce,
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Error creating form:', error);
        throw error;
    }
}

export const updateForm = async (endpoint: string, nonce: string, form_id: number, formState: string) => {
    try {
        const response = await fetch(`${endpoint}?action=dock_funnel_ajax_update_form`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-WP-Nonce': nonce,
            },
            body: JSON.stringify({
                form_id: form_id,
                form_state: formState,
                nonce: nonce,
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Error updating form:', error);
        throw error;
    }
}

export const deleteForm = async (endpoint: string, nonce: string, formId: number) => {
    try {
        const response = await fetch(`${endpoint}?action=dock_funnel_ajax_delete_form`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-WP-Nonce': nonce,
            },
            body: JSON.stringify({
                form_id: formId,
                nonce: nonce,
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Error deleting form:', error);
        throw error;
    }
}

export const getFormById = async (endpoint: string, nonce: string, formId: number) => {
    try {
        const response = await fetch(`${endpoint}?action=dock_funnel_ajax_get_form`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-WP-Nonce': nonce,
            },
            body: JSON.stringify({
                form_id: formId,
                nonce: nonce,
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Error fetching form by ID:', error);
        throw error;
    }
}

export const getFormResponses = async (endpoint: string, nonce: string, formId: number) => {
    try {
        const response = await fetch(`${endpoint}?action=dock_funnel_ajax_get_responses`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-WP-Nonce': nonce,
            },
            body: JSON.stringify({
                form_id: formId,
                nonce: nonce,
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Error fetching form responses:', error);
        throw error;
    }
}

export const deleteFormResponse = async (endpoint: string, nonce: string, form_id: number, responseId: number) => {
    try {
        const response = await fetch(`${endpoint}?action=dock_funnel_ajax_delete_form_response`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-WP-Nonce': nonce,
            },
            body: JSON.stringify({
                form_id: form_id,
                response_id: responseId,
                nonce: nonce,
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        console.log("Delete response response:", response);
        return await response.json();
    } catch (error) {
        console.error('Error deleting form response:', error);
        throw error;
    }
}

type FormSubmitResponseType = {
    success: boolean;
    data: {
        message: string;
        redirect_url?: string; // Optional redirect URL
        submission_id?: number; // Optional submission ID
    }
}
export const submitFormResponse = async (endpoint: string, nonce: string, formSubmission: Record<string, any>) : Promise<FormSubmitResponseType> => {
    try {
        const response = await fetch(`${endpoint}?action=dock_funnel_ajax_submit_form`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-WP-Nonce': nonce,
            },
            body: JSON.stringify({
                form_submission: formSubmission,
                nonce: nonce,
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Error submitting form response:', error);
        throw error;
    }
}