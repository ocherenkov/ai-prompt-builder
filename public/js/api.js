class API {
    constructor() {
        this.baseUrl = '/api';
        this.csrfToken = null;
        this.user = null;
        this.promptDraft = null;
    }

    // Auth methods
    async checkAuth() {
        try {
            if (!this.user) {
                const response = await this.request('/auth/user');
                this.user = response.user;
            }
            this.updateAuthUI();
            return true;
        } catch (error) {
            this.user = null;
            this.updateAuthUI();
            return false;
        }
    }

    async login(email, password) {
        const response = await this.request('/auth/login', {
            method: 'POST',
            body: JSON.stringify({email, password})
        });
        this.user = response.user;
        this.updateAuthUI();
        return response;
    }

    async register(name, email, password, password_confirmation) {
        const response = await this.request('/auth/register', {
            method: 'POST',
            body: JSON.stringify({name, email, password, password_confirmation})
        });
        this.user = response.user;
        this.updateAuthUI();
        return response;
    }

    async logout() {
        await this.request('/auth/logout', {method: 'POST'});
        this.user = null;
        this.updateAuthUI();
    }

    // Prompt methods
    async getPrompt(id) {
        return await this.request(`/prompts/${id}`);
    }

    async updatePrompt(id, data) {
        return await this.request(`/prompts/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    async createPrompt(data) {
        return await this.request('/prompts', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    async getPrompts() {
        return this.request('/prompts');
    }

    async deletePrompt(id) {
        return await this.request(`/prompts/${id}`, {
            method: 'DELETE'
        });
    }

    // Categories
    async getCategories() {
        return this.request('/categories');
    }

    async getPromptsByCategory(categoryId) {
        return this.request(`/prompts/category/${categoryId}`);
    }

    // Ratings
    async ratePrompt(promptId, rating) {
        return this.request(`/prompts/${promptId}/rate`, {
            method: 'POST',
            body: JSON.stringify({rating})
        });
    }

    // Draft methods
    savePromptDraft(draft) {
        this.promptDraft = draft;
        localStorage.setItem('promptDraft', JSON.stringify(draft));
    }

    getPromptDraft() {
        if (!this.promptDraft) {
            const saved = localStorage.getItem('promptDraft');
            this.promptDraft = saved ? JSON.parse(saved) : null;
        }
        return this.promptDraft;
    }

    clearPromptDraft() {
        this.promptDraft = null;
        localStorage.removeItem('promptDraft');
    }

    // UI methods
    updateAuthUI() {
        const authButtons = document.getElementById('authButtons');
        const userMenu = document.getElementById('userMenu');
        const userName = document.getElementById('userName');
        const userMenuButton = document.getElementById('userMenuButton');
        const userMenuDropdown = document.getElementById('userMenuDropdown');
        const logoutButton = document.getElementById('logoutButton');

        if (this.user) {
            // Show user menu and hide auth buttons
            authButtons.classList.add('hidden');
            userMenu.classList.remove('hidden');
            userName.textContent = this.user.name;

            // Setup dropdown toggle
            userMenuButton.onclick = () => {
                userMenuDropdown.classList.toggle('hidden');
            };

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!userMenu.contains(e.target)) {
                    userMenuDropdown.classList.add('hidden');
                }
            });

            // Setup logout button
            logoutButton.onclick = () => {
                this.logout();
                userMenuDropdown.classList.add('hidden');
                router.navigate('home');
            };
        } else {
            // Show auth buttons and hide user menu
            authButtons.classList.remove('hidden');
            userMenu.classList.add('hidden');
            userName.textContent = '';
        }
    }

    // Helper methods
    async getCsrfToken() {
        if (!this.csrfToken) {
            const data = await this.request(`/csrf-token`);
            this.csrfToken = data.token;
        }
        return this.csrfToken;
    }

    async request(endpoint, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        };

        if (['POST', 'PUT', 'DELETE'].includes(options.method)) {
            defaultOptions.headers['X-CSRF-TOKEN'] = await this.getCsrfToken();
        }

        const response = await fetch(`${this.baseUrl}${endpoint}`, {
            ...defaultOptions,
            ...options
        });

        this.csrfToken = null;

        const result = await response.json();

        if (!response.ok) {
            if (result.code === 422) {
                const validationMessages = this.formatValidationErrors(result.errors);
                window.showToast(validationMessages.join("\n"), 'error');
            }
            window.showToast(result.error, 'error');
        }

        if (!result.success) {
            throw new Error('API request failed');
        }

        return result.data;
    }

    formatValidationErrors(errors) {
        const messages = [];
        for (const field in errors) {
            if (Array.isArray(errors[field])) {
                errors[field].forEach(message => messages.push(message));
            }
        }
        return messages;
    }

}
