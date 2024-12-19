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
All requests require an API key in the header:
```javascript
headers: {
  'Authorization': 'Bearer YOUR_API_KEY'
}
```

### React Setup
```bash
# Install dependencies
npm install @portfolioapi/react axios

# Initialize in your React app
import { PortfolioClient } from '@portfolioapi/react';

const client = new PortfolioClient('YOUR_API_KEY');

// Example usage
function ProjectList() {
  const [projects, setProjects] = useState([]);

  useEffect(() => {
    const fetchProjects = async () => {
      const data = await client.projects.getAll();
      setProjects(data);
    };
    fetchProjects();
  }, []);
}
```

### Vue Setup
```bash
# Install dependencies
npm install @portfolioapi/vue axios

# Initialize in your Vue app
import { createPortfolioClient } from '@portfolioapi/vue';

const client = createPortfolioClient('YOUR_API_KEY');

// Example usage
export default {
  data() {
    return {
      projects: []
    }
  },
  async mounted() {
    this.projects = await client.projects.getAll();
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
  "title": "E-commerce Platform",
  "description": "Modern e-commerce solution",
  "technologies": ["React", "Node.js"],
  "imageUrl": "https://example.com/project.jpg",
  "startDate": "2024-01-01",
  "endDate": "2024-12-31",
  "status": "completed"
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
  "name": "React",
  "level": "Expert",
  "category": "technical",
  "yearsOfExperience": 5,
  "certifications": ["Meta React Developer"]
}
```

### Contact
Base URL: `/api/contact`

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/email` | Send contact email |

**POST Request Body:**
```javascript
{
  "name": "John Doe",
  "email": "john@example.com",
  "subject": "Project Inquiry",
  "message": "I'm interested in discussing a potential project.",
  "priority": "normal",
  "attachments": [] // Base64 encoded files, optional
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