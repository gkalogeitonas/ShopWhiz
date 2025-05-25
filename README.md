# ShopWhiz: Multi-Tenant AI-Powered Conversational Product Assistant

ShopWhiz is a multi-tenant AI-powered conversational product assistant for e-shops. It combines semantic search via vector databases (Qdrant) with structured filtering (Meilisearch) to provide a natural language interface for product discovery.

## Features

- **Conversational AI**: Natural language understanding of product queries
- **Semantic Search**: Vector database (Qdrant) for semantic matching
- **Structured Filtering**: Meilisearch for faceted filtering and full-text search
- **Multi-Tenant Support**: Isolated data environments per tenant
- **Feed-Based Input**: Support for Google Merchant, Skroutz XML, and JSON formats
- **Context Management**: Maintains chat context across multiple turns
- **API-First Architecture**: Easy integration with e-commerce platforms

## Tech Stack

- **Backend**: Laravel 10
- **LLM**: Neuron AI (with OpenAI embeddings)
- **Vector DB**: Qdrant
- **Search Engine**: Meilisearch
- **Database**: MySQL 8
- **Authentication**: Laravel Sanctum
- **Container Orchestration**: Docker & Docker Compose

## Getting Started

### Prerequisites

- Docker and Docker Compose
- PHP 8.2+ (for local development)
- Composer
- Node.js & npm
- OpenAI API key (for embeddings)

### Installation

See the detailed installation instructions in [Docker README](docker/README.md).

For quick development setup:

```bash
# Clone the repository
git clone https://github.com/yourusername/shopwhiz.git
cd shopwhiz

# Install dependencies
composer install
npm install

# Set up environment variables
cp .env.example .env
php artisan key:generate

# Start required services (Meilisearch and Qdrant)
docker-compose -f docker-compose.dev.yml up -d

# Run migrations
php artisan migrate

# Start the development server
php artisan serve
```

## Project Documentation

- [Product Requirements Document (PRD)](doc/prd.md)
- [Docker Setup Guide](docker/README.md)

## System Architecture

ShopWhiz follows a multi-tenant architecture where each e-shop tenant has isolated data in:

1. **Vector database** (Qdrant) for semantic search
2. **Search engine** (Meilisearch) for structured filtering
3. **Relational database** (MySQL) for tenant and session management

The conversational AI layer builds on top of these services to provide natural language understanding of product queries.

## API Endpoints

ShopWhiz exposes two main API endpoints:

- `/api/chat`: For natural language product queries
- `/api/feed/import`: For uploading/syncing product feeds

See the API documentation for more details.

## License

This project is proprietary software. All rights reserved.

# ShopWhiz
