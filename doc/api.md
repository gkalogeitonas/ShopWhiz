# ShopWhiz API Documentation

This document outlines the API endpoints exposed by ShopWhiz for tenant integration.

## Authentication

All API requests require authentication via an API token. Include the token in each request:

```
Authorization: Bearer YOUR_API_TOKEN
```

## Endpoints

### Chat API

**Endpoint:** `/api/chat`  
**Method:** POST  
**Description:** Send a natural language query to search for products.

**Request Body:**
```json
{
  "query": "I want waterproof hiking boots under €100",
  "session_id": "unique-session-identifier",
  "user_id": "optional-user-identifier"
}
```

**Response:**
```json
{
  "message": "I found 12 waterproof hiking boots under €100. Here are the top options:",
  "products": [
    {
      "id": "123",
      "title": "Nike Waterproof Hiking Boot",
      "price": "€89.99",
      "brand": "Nike",
      "image_url": "https://example.com/image.jpg",
      "category": "Footwear > Hiking",
      "url": "https://yourstore.com/product/123"
    },
    {
      "id": "456",
      "title": "Adidas Outdoor Terrex Swift R3 GTX",
      "price": "€95.00",
      "brand": "Adidas",
      "image_url": "https://example.com/image2.jpg",
      "category": "Footwear > Hiking",
      "url": "https://yourstore.com/product/456"
    }
  ],
  "has_products": true,
  "product_count": 12,
  "follow_up_suggestions": [
    "Filter by specific size",
    "Show similar from Adidas",
    "See lighter weight options"
  ]
}
```

### Feed Import API

**Endpoint:** `/api/feed/import`  
**Method:** POST  
**Description:** Trigger a product feed import from the specified URL.

**Request Body:**
```json
{
  "feed_url": "https://yourstore.com/products/feed.xml",
  "feed_type": "google_merchant"
}
```

**Feed Types:**
- `google_merchant`: Google Merchant XML format
- `skroutz`: Skroutz XML format
- `json`: Custom JSON format

**Response:**
```json
{
  "success": true,
  "message": "Feed import job has been queued",
  "job_id": "feed-import-123456"
}
```

### Feed Import Status API

**Endpoint:** `/api/feed/status/{job_id}`  
**Method:** GET  
**Description:** Check the status of a feed import job.

**Response:**
```json
{
  "status": "completed",
  "message": "Feed import completed successfully",
  "stats": {
    "total_products": 1250,
    "products_created": 250,
    "products_updated": 980,
    "products_deleted": 20,
    "errors": 0
  },
  "completed_at": "2023-05-25T14:30:45Z"
}
```

## Error Responses

**Authentication Error:**
```json
{
  "error": "authentication_failed",
  "message": "Invalid API token"
}
```

**Validation Error:**
```json
{
  "error": "validation_failed",
  "message": "The provided data was invalid",
  "errors": {
    "query": ["The query field is required"]
  }
}
```

**Server Error:**
```json
{
  "error": "server_error",
  "message": "An unexpected error occurred"
}
```

## Rate Limits

API calls are limited to:
- 100 chat requests per minute per tenant
- 10 feed imports per day per tenant

## Webhooks (Coming Soon)

We plan to add webhook functionality for:
- Feed import completion notifications
- Error notifications
- Usage reports
