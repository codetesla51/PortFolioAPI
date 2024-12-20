<div align="center">

# PortfolioAPI

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://semver.org)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg)](https://github.com/yourusername/portfolioapi)

A modern REST API for managing professional portfolios
</div>

## Table of Contents
- [Overview](#overview)
- [Key Features](#key-features)
- [Getting Started](#getting-started)
- [Framework Integration](#framework-integration)
- [API Resources](#api-resources)
- [Response Codes](#response-codes)
- [Contributing](#contributing)
- [License](#license)

## Overview

PortfolioAPI is a comprehensive backend solution for professional portfolio websites. It provides robust endpoints for managing projects, skills, reviews, and professional experience, making it ideal for developers, designers, and creatives who need a reliable API for their portfolio sites.

## Key Features

- **Project Management**: Showcase and manage portfolio projects
- **Skills Tracking**: Document and update professional skills
- **Review System**: Collect and display testimonials
- **Experience Logging**: Track professional experience
- **Contact Management**: Handle incoming communications

## Getting Started

### Authentication

All API requests require authentication via an API key. Obtain your key through
our [API Registration Page](https://portfolio-api-gui.vercel.app/).

Include your API key in request headers:

```javascript
headers: {
  'Authorization': 'Bearer YOUR_API_KEY'
}
```
### API HOST  

The base URL for the Portfolio API is:  
```bash  
https://api-portfolio-v1.vercel.app/  
```
### Framework Integration

#### React Integration

```javascript
import axios from 'axios';
import { useState, useEffect } from 'react';

function ProjectList() {
  const [projects, setProjects] = useState([]);

  useEffect(() => {
    const fetchProjects = async () => {
      try {
        const response = await axios.get('https://api.example.com/projects', {
          headers: {
            'Authorization': 'Bearer YOUR_API_KEY',
          },
        });
        setProjects(response.data);
      } catch (error) {
        console.error('Error fetching projects:', error);
      }
    };
    fetchProjects();
  }, []);
}
```

#### Vue Integration

```javascript
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
      });
      this.projects = response.data;
    } catch (error) {
      console.error('Error fetching projects:', error);
    }
  }
}
```

#### Svelte Integration

```javascript
import axios from 'axios';
import { onMount } from 'svelte';

let projects = [];

onMount(async () => {
  try {
    const response = await axios.get('https://api.example.com/projects', {
      headers: {
        'Authorization': 'Bearer YOUR_API_KEY',
      },
    });
    projects = response.data;
  } catch (error) {
    console.error('Error fetching projects:', error);
  }
});
```

## API Resources

### Projects

Manage portfolio projects with full CRUD operations.

**Base URL**: `/api/projects`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | List all projects |
| GET | `/:id` | Get project details |
| POST | `/` | Create new project |
| PUT | `/:id` | Update project |
| DELETE | `/:id` | Delete project |

**Request Body Example**:
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

Track and showcase professional skills.

**Base URL**: `/api/skills`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | List all skills |
| GET | `/:category` | Get skills by category |
| POST | `/` | Add new skill |
| PUT | `/:id` | Update skill |
| DELETE | `/:id` | Remove skill |

**Request Body Example**:
```javascript
{
  "skill_name": "PHP Programming",
  "experience_level": "Intermediate",
  "years_of_experience": 3,
  "description": "Experienced in PHP for backend web development."
}
```

### Reviews

Manage testimonials and feedback.

**Base URL**: `/api/reviews`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | List all reviews |
| GET | `/:id` | Get review details |
| POST | `/` | Add new review |
| PUT | `/:id` | Update review |
| DELETE | `/:id` | Remove review |

**Request Body Example**:
```javascript
{
reviewer_name": "John Doe", 
  "rating": 4, 
  "review_text": "Great product, highly recommend it."
  "reviewer_job_title": "Indie Hacker",
}
```

### Experience

Log professional experience and work history.

**Base URL**: `/api/experience`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | List all experience |
| GET | `/:id` | Get experience details |
| POST | `/` | Add new experience |
| PUT | `/:id` | Update experience |
| DELETE | `/:id` | Remove experience |

**Request Body Example**:
```javascript
{
  "company_name": "Tech Corp",
  "role": "Software Engineer",
  "start_date": "2022-01-01",
  "end_date": "2024-12-01",
  "description": "Worked on developing web applications."
}
```

### Contact Management

Handle incoming communications through a templated email system.

**Base URL**: `/api/email`

**Key Features**:
- Pre-built email templates
- Secure email receiving
- Customizable recipient configuration

**Request Body Example**:
```javascript
{
  "recipient": "Example@gmail.com",
  "subject": "Test Email",
  "sender_email": "sender@example.com",
  "sender_name": "Sender Name",
  "body": "This is a test email body"
}
```

## Response Codes

| Code | Status | Description | Example Response |
|------|---------|------------|------------------|
| 200 | OK | Request successful | `{"status": "success", "data": {...}}` |
| 201 | Created | Resource created | `{"status": "success", "data": {"id": "123"}}` |
| 400 | Bad Request | Invalid input | `{"status": "error", "message": "Invalid input data"}` |
| 401 | Unauthorized | Invalid API key | `{"status": "error", "message": "Invalid API key"}` |
| 403 | Forbidden | Insufficient permissions | `{"status": "error", "message": "Access denied"}` |
| 404 | Not Found | Resource/Email not found | `{"status": "error", "message": "Resource not found"}` |
| 429 | Too Many Requests | Daily rate limit exceeded | `{"status": "error", "message": "Rate limit exceeded"}` |
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
🌟  Star the Project if You Find It Useful
Created by uthmandev