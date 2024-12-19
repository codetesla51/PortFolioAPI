<div align="center">

# PortfolioAPI

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://semver.org)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg)](https://github.com/yourusername/portfolioapi)

 A modern REST API for managing professional portfolios
</div>

## Table of Contents
- [Overview](#overview)
- [API Endpoints](#api-endpoints)
- [Quick Start](#quick-start)
- [Response Codes](#response-codes)
- [Contributing](#contributing)

## Overview

PortfolioAPI powers professional portfolio websites with features like project management, skills showcase, and contact handling. Perfect for developers, designers, and creatives who want a robust backend for their portfolio.

## API Endpoints

###  Projects
Base URL: `/api/projects`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | List all projects |
| GET | `/:id` | Get project details |
| POST | `/` | Create new project |
| PUT | `/:id` | Update project |
| DELETE | `/:id` | Delete project |

**Example Response (200 OK):**
```javascript
{
  "status": "success",
  "data": {
    "id": "1",
    "title": "E-commerce Platform",
    "description": "Modern e-commerce solution",
    "technologies": ["React", "Node.js"],
    "imageUrl": "https://example.com/project.jpg"
  }
}
```

###  Skills
Base URL: `/api/skills`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | List all skills |
| GET | `/:category` | Get skills by category |
| POST | `/` | Add new skill |
| PUT | `/:id` | Update skill |
| DELETE | `/:id` | Remove skill |

**Example Response (201 Created):**
```javascript
{
  "status": "success",
  "data": {
    "id": "1",
    "name": "React",
    "level": "Expert",
    "category": "technical",
    "yearsOfExperience": 5
  }
}
```

###  Reviews
Base URL: `/api/reviews`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | List all reviews |
| GET | `/:id` | Get review details |
| POST | `/` | Submit review |
| PUT | `/:id` | Update review |
| DELETE | `/:id` | Delete review |

**Example Response (200 OK):**
```javascript
{
  "status": "success",
  "data": {
    "id": "1",
    "clientName": "John Doe",
    "rating": 5,
    "review": "Outstanding work!",
    "date": "2024-12-19"
  }
}
```

###  Experience
Base URL: `/api/experience`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | List all experience |
| GET | `/:id` | Get experience details |
| POST | `/` | Add experience |
| PUT | `/:id` | Update experience |
| DELETE | `/:id` | Delete experience |

**Example Response (201 Created):**
```javascript
{
  "status": "success",
  "data": {
    "id": "1",
    "position": "Senior Developer",
    "company": "Tech Corp",
    "duration": "2022-2024",
    "highlights": ["Led team of 5", "Improved performance by 50%"]
  }
}
```

###  Contact
Base URL: `/api/contact`

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/email` | Send contact email |
| POST | `/subscribe` | Newsletter subscription |
| PUT | `/preferences/:id` | Update preferences |
| DELETE | `/unsubscribe/:id` | Unsubscribe |

**Example Response (200 OK):**
```javascript
{
  "status": "success",
  "data": {
    "id": "1",
    "message": "Email sent successfully",
    "timestamp": "2024-12-19T10:00:00Z"
  }
}
```

## Response Codes

| Code | Status | Description |
|------|---------|------------|
| 200 | OK | Request successful |
| 201 | Created | Resource created |
| 400 | Bad Request | Invalid input |
| 401 | Unauthorized | Authentication required |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Resource not found |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Server Error | Internal server error |

## Quick Start

```javascript
// Example: Fetch projects
const response = await fetch('https://api.portfolio.com/api/projects', {
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN'
  }
});
const data = await response.json();
```

## Contributing

1. Fork the repository
2. Create your feature branch: `git checkout -b feature/amazing-feature`
3. Commit your changes: `git commit -m 'Add amazing feature'`
4. Push to the branch: `git push origin feature/amazing-feature`
5. Open a Pull Request

---

ðŸ“š [Documentation](https://docs.portfolioapi.com) | ðŸ› [Report
Bug](https://github.com/yourusername/portfolioapi/issues) | ðŸ’¡ [Request
Feature](https://github.com/yourusername/portfolioapi/issues)