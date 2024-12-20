<div align="center">

# PortfolioAPI

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://semver.org)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg)](https://github.com/yourusername/portfolioapi)

A modern REST API for managing professional portfolios
</div>

## Table of Contents
- [Overview](#overview)
- [Features](#features)
- [Quick Start](#quick-start)
- [API Endpoints](#api-endpoints)
- [Response Codes](#response-codes)
- [Contributing](#contributing)

## Overview

PortfolioAPI powers professional portfolio websites with features like project management, skills showcase, and contact handling. Perfect for developers, designers, and creatives who want a robust backend for their portfolio.

## Features
- Project Management
- Skills Tracking
- Review System
- Experience Logging
- Contact Management

## Quick Start

### Authentication
All requests require an API key in the header. You can get your API key by registering on our [API Registration Page](https://example.com/register).

To authenticate your requests, include the API key in the request header like this:

```javascript
headers: {
  'Authorization': 'Bearer YOUR_API_KEY'
}
```
Replace ```YOUR_API_KEY``` with the API key you received after registration. Make sure to keep your API key secure and avoid exposing it in public repositories or client-side code.

### React Setup
```javascript
// Initialize in your React app
import axios from 'axios';
import { useState, useEffect } from 'react';

const apiClient = axios.create({
  baseURL: 'https://api.example.com', // Replace with your API base URL
  headers: {
    'Authorization': 'Bearer YOUR_API_KEY',
  },
});

function ProjectList() {
  const [projects, setProjects] = useState([]);

  useEffect(() => {
    const fetchProjects = async () => {
      try {
        const response = await axios.get('/projects'); 
        setProjects(response.data); 
      } catch (error) {
        console.error('Error fetching projects:', error);
      }
    };
    fetchProjects();
  }, []);
}
```

### Vue Setup
```javascript
// Initialize in your Vue app
import axios from 'axios';

export default {
  data() {
    return {
      projects: []
    }
  },
  async mounted() {
    try {
      const response = await axios.get('https://api.example.com/projects', {
        headers: {
          'Authorization': 'Bearer YOUR_API_KEY',
        },
      }); // API call using axios
      this.projects = response.data;  // Set the fetched data
    } catch (error) {
      console.error('Error fetching projects:', error);
    }
  }
}
```

### Svelte Setup
```bash
# Install dependencies
npm install @portfolioapi/svelte axios

# Initialize in your Svelte app
import { PortfolioClient } from '@portfolioapi/svelte';

const client = new PortfolioClient('YOUR_API_KEY');

// Example usage
let projects = [];

onMount(async () => {
  projects = await client.projects.getAll();
});
```

## API Endpoints

### Projects
Base URL: `/api/projects`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | List all projects |
| GET | `/:id` | Get project details |
| POST | `/` | Create new project |
| PUT | `/:id` | Update project |
| DELETE | `/:id` | Delete project |

**POST Request Body:**
```javascript

  {
  "title": "My New Project", 
  "image": "https://example.com/image.jpg", 
  "description": "This is a description of my project.", 
  "tech_stack": ["PHP", "MySQL", "JavaScript"], 
  "start_date": "2024-01-01", 
  "finish_date": "2024-12-31", 
  "github_link": "https://github.com/user/project", 
  "live_link": "https://project.example.com"

}
```

### Skills
Base URL: `/api/skills`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | List all skills |
| GET | `/:category` | Get skills by category |
| POST | `/` | Add new skill |
| PUT | `/:id` | Update skill |
| DELETE | `/:id` | Remove skill |

**POST Request Body:**
```javascript
{
  "skill_name": "PHP Programming", 
  "experience_level": "Intermediate", 
  "years_of_experience": 3, 
  "description": "Experienced in PHP for backend web development."
}
```
### Reviews
Base URL: `/api/reviews`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | List all skills |
| GET | `/:category` | Get skills by category |
| POST | `/` | Add new skill |
| PUT | `/:id` | Update skill |
| DELETE | `/:id` | Remove skill |

**POST Request Body:**
```javascript
{
  "reviewer_name": "John Doe", 
  "rating": 4, 
  "review_text": "Great product, highly recommend it."
}
```
### Experience
Base URL: `/api/experience`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | List all skills |
| GET | `/:category` | Get skills by category |
| POST | `/` | Add new skill |
| PUT | `/:id` | Update skill |
| DELETE | `/:id` | Remove skill |

**POST Request Body:**
```javascript

  {
  "company_name": "Tech Corp",
  "role": "Software Engineer",
  "start_date": "2022-01-01",
  "end_date": "2024-12-01",
  "description": "Worked on developing web applications."
}

```

### ReceivingEmails
Base URL: `/api/email`

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/email` | Send contact email |

**POST Request Body:**
```javascript
{
  "recipient": "example@domain.com",
  "subject": "Portfolio Inquiry",
  "sender_email": "sender@domain.com",
  "sender_name": "John Doe",
  "template_id": 1
}
```

## Response Codes

| Code | Status | Description | Example Response |
|------|---------|------------|------------------|
| 200 | OK | Request successful | `{"status": "success", "data": {...}}` |
| 201 | Created | Resource created | `{"status": "success", "data": {"id": "123"}}` |
| 400 | Bad Request | Invalid input | `{"status": "error", "message": "Invalid input data"}` |
| 401 | Unauthorized | Authentication required | `{"status": "error", "message": "Invalid API key"}` |
| 403 | Forbidden | Insufficient permissions | `{"status": "error", "message": "Access denied"}` |
| 404 | Not Found | Resource not found | `{"status": "error", "message": "Resource not found"}` |
| 429 | Too Many Requests | Rate limit exceeded | `{"status": "error", "message": "Rate limit exceeded"}` |
| 500 | Server Error | Internal server error | `{"status": "error", "message": "Internal server error"}` |

## Contributing

1. Fork the repository
2. Create your feature branch: `git checkout -b feature/amazing-feature`
3. Commit your changes: `git commit -m 'Add amazing feature'`
4. Push to the branch: `git push origin feature/amazing-feature`
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

Created by the PortfolioAPI Team