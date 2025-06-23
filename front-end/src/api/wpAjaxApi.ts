export const createForm = async (endpoint: string, nonce: string, formData: string) => {
    try {
        const response = await fetch(`${endpoint}?action=dock_funnel_ajax_create_form`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-WP-Nonce': nonce,
            },
            body: JSON.stringify({
                form_data: formData,
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

export const updateForm = async (endpoint: string, nonce: string, form_id: number, formData: string) => {
    try {
        const response = await fetch(`${endpoint}?action=dock_funnel_ajax_update_form`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-WP-Nonce': nonce,
            },
            body: JSON.stringify({
                form_id: form_id,
                form_data: formData,
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


export const submitFormResponse = async (endpoint: string, nonce: string, formSubmission: Record<string, any>) => {
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