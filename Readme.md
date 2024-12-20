<div align="center">
  <img
  src="server-path-svgrepo-com.svg"
  alt="PortfolioAPI Server Screenshot" width="70">
</p>

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

- **Project Management**: Add and manage portfolio projects programmatically, including titles, descriptions, images, and links.

- **Skills Tracking**: Track and update professional skills in your portfolio via API.

- **Review & Documentation System**: Add and display testimonials or reviews to enhance your portfolio.

- **Experience Logging**: Log professional experience, including company name, role, start and end dates, and job description.

- **Contact Management**: Manage incoming communications using customizable email templates.

## Getting Started

### Authentication

All API requests require authentication via an API key. Obtain your key through
our [API Registration Page](https://portfolio-api-gui.vercel.app/).

Include your API key in request headers:

```javascript
headers: {
Authorization: Bearer YOUR_API_KEY
  
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
> ### Note on Pagination with Limit and Offset
> 
> You can paginate your GET requests by adding `limit` and `offset` as query parameters in the URL. 
> 
> - **limit**: Defines the number of results to be returned in a single request.
> - **offset**: Defines the number of records to skip before starting to return results.
> 
> For example, to fetch the first 5 records, you can use the following URL:
> 
> ```
> https://api.example.com/projects?limit=5&offset=0
> ```
> 
> You can modify the `limit` and `offset` values to control the data you receive, making it easier to handle large datasets efficiently.
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
  "reviewer_name": "John Doe", 
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
- Pre-built email templates (5 in total: 2 light, 2 dark, 1 default)
- Secure email receiving
- Customizable recipient configuration

**Email Template Types**:
- **Default Template** (Template 1): The default layout used for emails.
- **Light Templates** (Templates 2 and 3): Light-themed templates designed for clean and minimalist emails.
- **Dark Templates** (Templates 4 and 5): Dark-themed templates suitable for modern, sleek email designs.

Each template is available for use based on your preference:
- Template 1 is the default and is applied if no other template is specified.
- Templates 2 and 3 are light-themed, ideal for simple and bright email content.
- Templates 4 and 5 are dark-themed, designed for a more professional and modern look.

**Additional Information**:
- **Sender Email**: The email you want to receive incoming emails with (i.e., the "from" email address).
- **Body**: The body of the email is optional. If no custom body is provided, the default body will be used. If you wish to send a personalized message, you can specify a custom body in the request.

If you do not specify a template, **Template 1 (default)** will be applied automatically, and you can include a custom body if desired.

**Request Body Example**:
```javascript

{
  "recipient": "test@gmail.com",
  "subject": "Test Email",
  "sender_email": "sender@example.com",
  "sender_name": "Sender Name",
  "body": "This is a custom email body", // only add if you want custom body
  "template_id": 1
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

---
🌟  Star the Project if You Find It Useful
Created by uthmandev