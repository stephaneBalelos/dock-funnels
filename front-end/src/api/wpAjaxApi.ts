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