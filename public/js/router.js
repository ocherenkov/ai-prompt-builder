class Router {
    constructor() {
        this.routes = {};

        window.addEventListener('popstate', () => {
            const hash = window.location.hash.slice(1) || 'home';
            const [route, params] = this.parseRoute(hash);
            this.navigate(route, this.parseParams(params));
        });

        document.addEventListener('click', (e) => {
            const routeLink = e.target.closest('[data-route]');
            if (routeLink) {
                e.preventDefault();
                const route = routeLink.dataset.route;
                const params = routeLink.dataset.params ? JSON.parse(routeLink.dataset.params) : null;
                this.navigate(route, params);
            }
        });
    }

    register(name, component) {
        this.routes[name] = component;
    }

    updateActiveLinks(routeName) {
        document.querySelectorAll('[data-route]').forEach(link => {
            if (link.dataset.route === routeName) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }

    parseRoute(hash) {
        const [route, params] = hash.split('?');
        return [route, params];
    }

    parseParams(paramString) {
        if (!paramString) return null;
        const params = {};
        const searchParams = new URLSearchParams(paramString);
        for (const [key, value] of searchParams) {
            params[key] = value;
        }
        return params;
    }

    buildUrl(routeName, data) {
        let url = `#${routeName}`;
        if (data) {
            const params = new URLSearchParams();
            Object.entries(data).forEach(([key, value]) => {
                if (value !== null && value !== undefined) {
                    params.append(key, value);
                }
            });
            const paramString = params.toString();
            if (paramString) {
                url += `?${paramString}`;
            }
        }
        return url;
    }

    init() {
        const hash = window.location.hash.slice(1) || 'home';
        const [route, params] = this.parseRoute(hash);
        this.navigate(route, this.parseParams(params));
    }

    async navigate(routeName, data = null) {

        if (routeName === 'login' || routeName === 'register') {
            if (window.api && await window.api.checkAuth()) {
                window.location.hash = '#home';
                return;
            }
        }

        const component = this.routes[routeName];
        if (!component) {
            console.error(`Route "${routeName}" not found`);
            return;
        }

        const mainContent = document.getElementById('main-content');
        if (!mainContent) {
            console.error('Main content container not found');
            return;
        }

        try {
            ui.showLoading(true);

            history.pushState(null, '', this.buildUrl(routeName, data));

            const content = await component(data);

            mainContent.innerHTML = content;
            this.updateActiveLinks(routeName);

            window.scrollTo(0, 0);
        } catch (error) {
            console.error('Navigation error:', error);
            mainContent.innerHTML = `
                <div class="p-4 bg-red-50 text-red-700 rounded-md">
                    <p>Error loading content. Please try again.</p>
                </div>
            `;
            ui.showToast('Error loading page', 'error');
        } finally {
            ui.showLoading(false);
        }

    }
}
