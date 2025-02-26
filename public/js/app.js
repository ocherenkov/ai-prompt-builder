// UI Helper functions
const ui = {
    showToast(message, type = 'success') {
        Toastify({
            text: message,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            backgroundColor: type === 'success' ? '#10B981' : '#EF4444',
            stopOnFocus: true,
        }).showToast();
    },

    showLoading(show = true) {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.style.display = show ? 'flex' : 'none';
        }
    }
};

// Initialize API and Router
const api = new API();
const router = new Router();

// Make helpers globally available
window.ui = ui;
window.api = api;
window.router = router;
window.showToast = ui.showToast;
window.showLoading = ui.showLoading;

// Components
const homeComponent = async () => {
    return `
        <div class="max-w-4xl mx-auto px-4 py-12">
            <div class="text-center mb-16">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl md:text-6xl">
                    <span class="block">Create Powerful AI Prompts</span>
                    <span class="block text-primary mt-2">With Ease</span>
                </h1>
                <p class="mt-6 text-xl text-gray-500">
                    Build, test, and share AI prompts that deliver consistent and high-quality results.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                <div class="card p-8 hover:shadow-lg">
                    <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Create New Prompt</h3>
                    <p class="text-gray-500 mb-6">Design and structure your AI prompts with our intuitive builder interface.</p>
                    <a href="#" data-route="create-prompt" class="btn btn-primary w-full">
                        Get Started
                    </a>
                </div>

                <div class="card p-8 hover:shadow-lg">
                    <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Browse Prompts</h3>
                    <p class="text-gray-500 mb-6">Explore and use pre-built prompts from our growing collection.</p>
                    <a href="#" data-route="prompts" class="btn btn-secondary w-full">
                        View Collection
                    </a>
                </div>
            </div>
        </div>
    `;
};

const promptsComponent = async () => {
    try {
        const categoryId = getParamFromUrl('categoryId');
        const [categories, prompts] = await Promise.all([
            api.getCategories(),
            categoryId ? api.getPromptsByCategory(categoryId) : api.getPrompts()
        ]);

        const activeCategoryId = categoryId || 'all';

        return `
            <div class="max-w-7xl mx-auto px-4 py-8">
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Prompts</h1>
                    <button onclick="router.navigate('create-prompt')" class="btn btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Create New
                    </button>
                </div>
                <div class="flex space-x-2 mb-6 overflow-x-auto pb-2">
                    <a 
                        href="#prompts" 
                        data-route="prompts"
                        class="${activeCategoryId === 'all' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700'} px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap hover:bg-primary hover:text-white transition-colors">
                        All Prompts
                    </a>
                    ${categories.map(category => `
                        <a 
                            href="#prompts?categoryId=${category.id}" 
                            data-route="prompts"
                            data-params='${JSON.stringify({categoryId: category.id})}'
                            class="${activeCategoryId == category.id ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700'} px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap hover:bg-primary hover:text-white transition-colors">
                            ${category.name}
                        </a>
                    `).join('')}
                </div>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    ${prompts.map(prompt => `
                        <div class="card p-6 hover:shadow-lg">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">${prompt.content}</h3>
                                ${renderPromptButtons(prompt)}
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-1 hidden">
                                    ${[1, 2, 3, 4, 5].map(star => `
                                        <button onclick="ratePrompt(${prompt.id}, ${star})" class="text-${prompt.rating >= star ? 'yellow' : 'gray'}-400">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </button>
                                    `).join('')}
                                </div>
                                <span class="text-sm text-gray-500">${new Date(prompt.created_at).toLocaleDateString()}</span>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    } catch (error) {
        return `
            <div class="rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Error loading prompts</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>${error.message}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
};

const createPromptComponent = async () => {
    const categories = await api.getCategories();
    const draft = api.getPromptDraft();
    return `
        <div class="max-w-4xl mx-auto px-4 py-8">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Create New Prompt</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Create a new AI prompt by filling out the sections below.
                        </p>
                    </div>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <form id="createPromptForm" class="space-y-6">
                        <div class="shadow sm:rounded-md sm:overflow-hidden">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="mb-4">
                                    <label for="category" class="block text-gray-600 font-medium mb-1">Category</label>
                                    <select id="category" name="category" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                                        <option value="" ${!draft?.category_id ? 'selected' : ''}>Select category</option>
                                        ${categories.map(category => `
                                                                <option value="${category.id}" ${draft?.category_id == category.id ? 'selected' : ''}>
                                                                    ${category.name}
                                                                </option>
                                                            `).join('')}
                                    </select>
                                </div>
                                <div id="promptSections" class="space-y-4">
                                    <div class="prompt-section bg-white p-4 mb-4 rounded-lg shadow" draggable="true" data-type="context">
                                        <div class="drag-handle flex items-center mb-2 text-gray-400 hover:text-gray-600 cursor-move">
                                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                            </svg>
                                            <span class="font-medium">Context</span>
                                        </div>
                                        <textarea id="context" name="context" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="You are an experienced copywriter...">${draft?.context || ''}</textarea>
                                    </div>
                                    <div class="prompt-section bg-white p-4 mb-4 rounded-lg shadow" draggable="true" data-type="task">
                                        <div class="drag-handle flex items-center mb-2 text-gray-400 hover:text-gray-600 cursor-move">
                                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                            </svg>
                                            <span class="font-medium">Task</span>
                                        </div>
                                        <textarea id="task" name="task" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Write a product description...">${draft?.task || ''}</textarea>
                                    </div>
                                    <div class="prompt-section bg-white p-4 mb-4 rounded-lg shadow" draggable="true" data-type="format">
                                        <div class="drag-handle flex items-center mb-2 text-gray-400 hover:text-gray-600 cursor-move">
                                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                            </svg>
                                            <span class="font-medium">Format</span>
                                        </div>
                                        <textarea id="format" name="format" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Use marketing style...">${draft?.format || ''}</textarea>
                                    </div>
                                </div>
                           
                                <!-- Preview -->
                                <div class="bg-white rounded-lg border p-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Preview</h4>
                                    <div id="promptPreview" class="text-sm text-gray-900"></div>
                                </div>
                            </div>
                            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 space-x-3">
                                <button type="button" onclick="saveDraft()" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                    Save Draft
                                </button>
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                    Create Prompt
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;
};

const editPromptComponent = async () => {
    try {
        const promptId = getParamFromUrl('id');
        const [categories, prompt] = await Promise.all([
            api.getCategories(),
            api.getPrompt(promptId)
        ]);

        return `
            <div class="max-w-4xl mx-auto px-4 py-8">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Edit Prompt</h3>
                            <p class="mt-1 text-sm text-gray-600">Edit your prompt details.</p>
                        </div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <form id="editPromptForm" class="space-y-6">
                            <div class="shadow sm:rounded-md sm:overflow-hidden">
                                <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                    <div class="mb-4">
                                        <label for="category" class="block text-gray-600 font-medium mb-1">Category</label>
                                        <select id="category" name="category" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                                            <option value="">Select category</option>
                                            ${categories.map(category => `
                                                <option value="${category.id}" ${category.id === prompt.category_id ? 'selected' : ''}>
                                                    ${category.name}
                                                </option>
                                            `).join('')}
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="context" class="block text-gray-600 font-medium mb-1">Context</label>
                                        <textarea id="context" name="context" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="You are an experienced copywriter...">${prompt.combinations?.context || ''}</textarea>
                                    </div>
                                    <div class="mb-4">
                                        <label for="task" class="block text-gray-600 font-medium mb-1">Task</label>
                                        <textarea id="task" name="task" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Write a product description...">${prompt.combinations?.task || ''}</textarea>
                                    </div>
                                    <div class="mb-4">
                                        <label for="format" class="block text-gray-600 font-medium mb-1">Format</label>
                                        <textarea id="format" name="format" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Use marketing style...">${prompt.combinations?.format || ''}</textarea>
                                    </div>
                                </div>
                                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Error loading prompt:', error);
        return `
            <div class="p-4 bg-red-50 text-red-700 rounded-md">
                <p>Error loading prompt. Please try again.</p>
            </div>
        `;
    }
};

const loginComponent = async () => {
    return `
        <div class="max-w-md mx-auto px-4 py-12 space-y-8">
                <div>
                    <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Sign in to your account</h2>
                </div>
                <form id="loginForm" class="mt-8 space-y-6">
                    <input type="hidden" name="remember" value="true">
                    <div class="rounded-md shadow-sm -space-y-px">
                        <div>
                            <label for="email" class="sr-only">Email address</label>
                            <input id="email" name="email" type="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" placeholder="Email address">
                        </div>
                        <div>
                            <label for="password" class="sr-only">Password</label>
                            <input id="password" name="password" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" placeholder="Password">
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Sign in
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;
};

const registerComponent = async () => {
    return `
        <div class="max-w-md mx-auto px-4 py-12 space-y-8">
                <div>
                    <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Create your account</h2>
                </div>
                <form id="registerForm" class="mt-8 space-y-6">
                    <div class="rounded-md shadow-sm -space-y-px">
                        <div>
                            <label for="name" class="sr-only">Full name</label>
                            <input id="name" name="name" type="text" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" placeholder="Full name">
                        </div>
                        <div>
                            <label for="email" class="sr-only">Email address</label>
                            <input id="email" name="email" type="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" placeholder="Email address">
                        </div>
                        <div>
                            <label for="password" class="sr-only">Password</label>
                            <input id="password" name="password" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" placeholder="Password">
                        </div>
                        <div>
                            <label for="password" class="sr-only">Password Confirmation</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm" placeholder="Password Confirmation">
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Register
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;
};

// Event Handlers

function saveDraft() {
    const form = document.getElementById('createPromptForm');
    const draft = {
        context: form.context.value,
        task: form.task.value,
        format: form.format.value,
        category_id: form.category.value
    };
    api.savePromptDraft(draft);
}

async function deletePrompt(id) {
    if (confirm('Are you sure you want to delete this prompt?')) {
        try {
            await api.deletePrompt(id);
            await router.navigate('prompts');
        } catch (error) {
            console.error('Failed to delete prompt:', error);
        }
    }
}

async function handleEditPrompt(event, id) {
    event.preventDefault();
    const form = event.target;
    const formData = getFormData(form);

    try {
        await api.updatePrompt(id, {
            category_id: parseInt(formData.category_id),
            content: {
                context: formData.context,
                task: formData.task,
                format: formData.format
            },
            raw: formData.raw
        });
        router.navigate('prompts');
        ui.showToast('Prompt updated successfully', 'success');
    } catch (error) {
        console.error('Error updating prompt:', error);
        ui.showToast('Failed to update prompt', 'error');
    }
}

function editPrompt(id) {
    router.navigate('edit-prompt', {id});
}

// Register routes with async components
router.register('home', async () => {
    const container = document.createElement('div');
    container.className = 'max-w-4xl mx-auto px-4';
    container.innerHTML = await homeComponent();
    return container.outerHTML;
});

router.register('prompts', async () => {
    const container = document.createElement('div');
    container.className = 'max-w-7xl mx-auto px-4';
    container.innerHTML = await promptsComponent();
    return container.outerHTML;
});

router.register('create-prompt', async () => {
    const container = document.createElement('div');
    container.className = 'max-w-3xl mx-auto px-4';
    container.innerHTML = await createPromptComponent();
    return container.outerHTML;
});

router.register('login', async () => {
    const container = document.createElement('div');
    container.className = 'max-w-md mx-auto px-4';
    container.innerHTML = await loginComponent();
    return container.outerHTML;
});

router.register('register', async () => {
    const container = document.createElement('div');
    container.className = 'max-w-md mx-auto px-4';
    container.innerHTML = await registerComponent();
    return container.outerHTML;
});

router.register('edit-prompt', async () => {
    const container = document.createElement('div');
    container.className = 'max-w-4xl mx-auto px-4';
    container.innerHTML = await editPromptComponent();
    return container.outerHTML;
});


// Helper functions

async function savePromptDraft() {
    const form = document.getElementById('createPromptForm');
    if (!form) return;

    const sections = document.querySelectorAll('#promptSections .prompt-section');
    const promptData = {};

    sections.forEach(section => {
        const type = section.getAttribute('data-type');
        const textarea = section.querySelector('textarea');
        if (type && textarea) {
            promptData[type] = textarea.value;
        }
    });

    if (form.category) {
        promptData.category_id = form.category.value;
    }

    api.savePromptDraft(promptData);
    ui.showToast('Черновик сохранен!', 'success');
}

function updatePromptPreview() {
    const preview = document.getElementById('promptPreview');
    if (!preview) return;

    const sections = document.querySelectorAll('#promptSections .prompt-section');
    const content = [];

    sections.forEach(section => {
        const textarea = section.querySelector('textarea');
        if (textarea && textarea.value.trim()) {
            content.push(textarea.value.trim());
        }
    });

    preview.textContent = content.join('\n\n');
}

document.addEventListener('input', (e) => {
    if (e.target.tagName === 'TEXTAREA') {
        updatePromptPreview();
    }
});

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    router.init();
    initDragAndDrop();
});

function initDragAndDrop() {
    let draggedItem = null;

    document.addEventListener('dragstart', (e) => {
        if (!e.target.classList.contains('prompt-section')) return;
        draggedItem = e.target;
        e.target.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
    });

    document.addEventListener('dragend', (e) => {
        if (!e.target.classList.contains('prompt-section')) return;
        e.target.classList.remove('dragging');
        updatePromptPreview();
    });

    document.addEventListener('dragover', (e) => {
        e.preventDefault();
        const container = document.getElementById('promptSections');
        if (!container) return;

        const afterElement = getDragAfterElement(container, e.clientY);
        const draggable = document.querySelector('.dragging');
        if (!draggable) return;

        if (afterElement) {
            container.insertBefore(draggable, afterElement);
        } else {
            container.appendChild(draggable);
        }
    });
}

function getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('.prompt-section:not(.dragging)')];

    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;

        if (offset < 0 && offset > closest.offset) {
            return {offset: offset, element: child};
        } else {
            return closest;
        }
    }, {offset: Number.NEGATIVE_INFINITY}).element;
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    // Login form handler
    document.body.addEventListener('submit', async (e) => {
        if (e.target.id === 'loginForm') {
            e.preventDefault();
            const email = e.target.email.value;
            const password = e.target.password.value;
            try {
                await api.login(email, password);
                const draft = api.getPromptDraft();
                if (draft) {
                    await router.navigate('create-prompt');
                } else {
                    await router.navigate('home');
                }
            } catch (error) {
                alert('Login failed: ' + error.message);
            }
        }
        // Register form handler
        else if (e.target.id === 'registerForm') {
            e.preventDefault();
            const name = e.target.name.value;
            const email = e.target.email.value;
            const password = e.target.password.value;
            const password_confirmation = e.target.password_confirmation.value;
            try {
                await api.register(name, email, password, password_confirmation);
                await router.navigate('home');
            } catch (error) {
                alert('Registration failed: ' + error.message);
            }
        }
        // Create prompt form handler
        else if (e.target.id === 'createPromptForm') {
            e.preventDefault();
            if (!api.user) {
                api.savePromptDraft(getFormData(e.target));
                await router.navigate('login');
                return;
            }

            try {
                await api.createPrompt(getFormData(e.target));
                api.clearPromptDraft();
                await router.navigate('prompts');
            } catch (error) {
                alert('Failed to create prompt: ' + error.message);
            }
        }
        // Edit prompt form handler
        else if (e.target.id === 'editPromptForm') {
            e.preventDefault();
            const id = getParamFromUrl('id');
            try {
                await api.updatePrompt(id, getFormData(e.target));
                await router.navigate('prompts');
            } catch (error) {
                alert('Failed to create prompt: ' + error.message);
            }
        }
    });
});

// Helper functions
const getParamFromUrl = (param) => {
    const hash = window.location.hash;

    const queryString = hash.split('?')[1];
    if (!queryString) return null;

    const params = new URLSearchParams(queryString);
    return params.get(param);
};

function getFormData(form) {
    const formData = new FormData(form);
    const category_id = formData.get('category');
    const context = formData.get('context');
    const task = formData.get('task');
    const format = formData.get('format');
    const raw = `${context} ${task} ${format}`;
    return {
        category_id,
        context,
        task,
        format,
        raw,
    };
}

const renderPromptButtons = (prompt) => {
    if (prompt.user_id === api.user?.id) {
        return `
            <div class="flex space-x-2">
                <button onclick="editPrompt(${prompt.id})" class="text-gray-400 hover:text-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
                <button onclick="deletePrompt(${prompt.id})" class="text-gray-400 hover:text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        `;
    }
    return '';
};
