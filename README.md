# AI Prompt Builder - Technical Documentation

## Architecture Overview

### Backend Stack

1. **PHP Framework**
    - Custom MVC framework implementation
    - Modular architecture with clear separation of concerns
    - Components organized in namespaced directories (Controllers, Models, Services, Repositories)

2. **Database Layer**
    - Migration system for database schema management
    - Seeder functionality for initial data population
    - Repository pattern for data access abstraction

3. **Core Components**
    - Routing system with support for RESTful endpoints
    - Service layer for business logic
    - Repository pattern for database operations

### Frontend Architecture

1. **JavaScript Implementation**
    - Vanilla JavaScript with modular organization
    - Custom router for SPA-like navigation
    - Component-based structure without framework dependencies

2. **CSS/Styling**
    - TailwindCSS for utility-first styling
    - Custom CSS for specific components
    - Responsive design implementation

## Key Features and Technical Solutions

### 1. Prompt Management System

- **Data Structure**
    - Prompts consist of three main sections: Context, Task, and Format
    - Category-based organization
    - Draft system for work-in-progress prompts

- **API Integration**
  ```javascript
  class API {
    // RESTful endpoints for CRUD operations
    getPrompts()
    createPrompt(data)
    updatePrompt(id, data)
    deletePrompt(id)
  }
  ```

### 2. Drag-and-Drop Implementation

- **Technical Solution**
    - Native HTML5 Drag and Drop API
    - Event-driven architecture for handling section reordering
    - Real-time preview updates
  ```javascript
  // Core drag-and-drop functionality
  initDragAndDrop() {
    // Event listeners for drag operations dragstart, dragend, dragover
    // Dynamic reordering logic
    getDragAfterElement()
  }
  ```

### 3. Real-time Preview System

- **Implementation Details**
    - Dynamic content updates based on section order
    - Efficient DOM manipulation
    - Event-based preview synchronization
  ```javascript
  updatePromptPreview() {
    // Collects content from sections in current order
    // Updates preview with formatted content
  }
  ```

### 4. State Management

- **Draft System**
    - Local storage for temporary saves
    - Automatic draft updates
    - Draft recovery functionality

- **User Session Management**
    - Authentication state tracking
    - User preferences persistence
    - Session-based features

### 5. UI/UX Features

- **Component Design**
    - Modular component structure
    - Reusable UI elements
    - Responsive layouts

- **Interactive Elements**
    - Drag handles for section reordering
    - Real-time feedback during interactions
    - Toast notifications for user actions

## Technical Decisions and Solutions

### 1. Framework Choice

- **Custom PHP Framework**
    - Lightweight and purpose-built
    - Easy to extend and maintain
    - Clear separation of concerns

- **Vanilla JavaScript**
    - No framework dependencies
    - Better performance
    - Easier maintenance

### 2. Performance Optimizations

- **Frontend**
    - Efficient DOM manipulation
    - Event delegation for better performance
    - Minimal dependencies

- **Backend**
    - Repository pattern for optimized data access
    - Efficient query handling
    - Proper caching implementation

### 3. Security Measures

- **API Security**
    - CSRF protection
    - Input validation
    - Secure session handling

- **Data Protection**
    - Proper escaping of user input
    - Validation of all user-submitted data
    - Secure storage of sensitive information

## Development Workflow

1. **Version Control**
    - Git-based workflow
    - Organized commit history
    - Feature branch development

2. **Development Environment**
    - Local development setup
    - Easy deployment process
    - Development/Production environment separation

## Future Considerations

1. **Scalability**
    - Current architecture supports easy scaling
    - Modular design allows for feature additions
    - Clear upgrade paths

2. **Maintenance**
    - Well-documented codebase
    - Clear coding standards
    - Easy to debug and extend

## Getting Started

### Prerequisites

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Composer for PHP dependencies
- Web server (Apache/Nginx)

### Installation

1. Clone the repository
   ```bash
   git clone git@github.com:ocherenkov/ai-prompt-builder.git
   ```

2. Install PHP dependencies
   ```bash
   composer install
   ```

3. Set up the database
   ```bash
   php migrate.php
   php seed.php
   ```

4. Configure your web server to point to the `public` directory

5. Access the application through your web browser

### Development

- The main application code is in the `app` directory
- Frontend assets are in the `public` directory
- Database migrations are in `database/migrations`
- Configuration files are in the `config` directory
