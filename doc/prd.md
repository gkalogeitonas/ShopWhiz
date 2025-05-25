**Project Requirements Document (PRD)**

**Project Title:**
Multi-Tenant AI-Powered Conversational Product Assistant for E-Shops

---

**1. Objective**

Develop an external, multi-tenant service that allows e-shops (using OpenCart or similar platforms) to integrate a chat-based product assistant via API. The assistant uses LLMs to interpret customer queries, replacing traditional filters with a conversational experience. It combines semantic search (vector database) with structured filtering (Meilisearch) for highly relevant product discovery.

---

**2. Key Features**

* Conversational assistant that guides users in filtering product listings
* LLM-powered natural language understanding and reasoning
* Semantic search via vector database (e.g., Qdrant)
* Structured filtering via Meilisearch
* Multi-tenant support with API token authentication
* Feed-based data input (Google Merchant / Skroutz XML or JSON)
* Maintains chat context to refine search over multiple turns

---

**3. System Components**

### 3.1 Backend (Laravel)

* Expose API endpoints:

  * `/api/chat` for natural language product queries
  * `/api/feed/import` for uploading/syncing product feeds
* Middleware for API token-based tenant authentication
* Services:

  * `VectorSearchService` (Qdrant integration)
  * `MeilisearchService` (indexing and filtering)
  * `LLMService` (Neuron AI integration)

### 3.2 Vector Database (e.g., Qdrant)

* Stores vector embeddings of product documents
* One collection or namespace per tenant
* Used for semantic matching of customer queries

### 3.3 Meilisearch

* Stores product documents in JSON format
* One index per tenant (`products_{tenant_id}`)
* Fields like brand, category, color, and price marked as filterable

### 3.4 LLM Layer (Neuron AI)

* Uses function-calling style tools:

  * `semanticSearchProducts(query)`
  * `filterProducts(filterJson)`
  * Optional: `getCategories()`, `getManufacturers()`
* Responsible for maintaining context across messages
* Always returns structured JSON response:
  ```json
  {
    "message": "I found 5 waterproof hiking boots under €100. Here are the best options:",
    "products": [...],
    "has_products": true,
    "follow_up_suggestions": ["Filter by size", "Show similar from other brands"]
  }
  ```
* Handles empty results gracefully with helpful alternative suggestions

### 3.5 Multi-Tenancy Model

* `tenants` table:

  ```
  id | name | api_token | meilisearch_index | vector_namespace
  ```
* Products are isolated per tenant in both Meilisearch and vector DB
* Each API call is authenticated via API token

---

**4. Chat Interaction Flow (Conversational Filtering UX)**

1. **User Input:** Customer types a natural request ("I want waterproof hiking boots under €100")
2. **LLM Request:** Message sent to Neuron AI with tool access
3. **Vector Search (semantic):**

   * `semanticSearchProducts("query")`
   * Embedding sent to Qdrant
   * Returns top product candidates (IDs + metadata)
4. **Filter Extraction:**

   * LLM builds filters like `price < 100`, `brand = 'Nike'`
   * Maintains chat context and updates filter state
5. **Structured Filtering:**

   * Call Meilisearch with combined filters: `id IN [...] AND brand = 'Nike' AND color = 'Black'`
6. **Results Returned:**

   * Products shown as responses in chat
7. **Follow-up Interaction:**

   * Assistant asks: "Want to filter by size or see similar products from Adidas?"
   * User responds naturally, and the cycle continues

---

**5. Feed Handling**

* Shop owner provides a product feed endpoint from their e-shop
* Scheduled periodic sync jobs (configurable interval per tenant):
  * Fetches product data from tenant's endpoint
  * Parses feed (supports Google Merchant, Skroutz XML, or JSON formats)
  * Generates normalized JSON products
  * Updates Meilisearch index
  * Generates embeddings using OpenAI embeddings API and updates vector DB
* Handles incremental updates and product deletions
* Maintains sync status and error logs per tenant

---

**6. Chat Session Management**

* Each conversation starts a new chat session
* Session-based context management:
  * Stores conversation history per session
  * Maintains filter state across multiple turns
  * Session expires after inactivity (configurable timeout)
* `chat_sessions` table structure:
  ```
  id | tenant_id | session_id | user_id | messages | filter_state | created_at | updated_at
  ```
* No persistent cross-session memory (fresh start each conversation)

---

**7. LLM Response Structure**

The LLM always returns a standardized JSON response regardless of query outcome:

**Successful Product Search:**
```json
{
  "message": "I found 12 waterproof hiking boots under €100. Here are the top options:",
  "products": [
    {
      "id": "123",
      "title": "Nike Waterproof Hiking Boot",
      "price": "€89.99",
      "brand": "Nike",
      "image_url": "...",
      "category": "Footwear > Hiking"
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

**No Products Found:**
```json
{
  "message": "I couldn't find any waterproof hiking boots under €50. However, I have some great alternatives:",
  "products": [],
  "has_products": false,
  "product_count": 0,
  "alternative_suggestions": [
    "Try increasing your budget to €75",
    "Consider water-resistant boots instead",
    "Look at trail running shoes with water protection"
  ],
  "follow_up_suggestions": [
    "Show me boots under €75",
    "What about water-resistant options?",
    "Show trail running shoes"
  ]
}
```

---

**8. Embedding Strategy**

* **Vector Embeddings:** OpenAI text-embedding-ada-002 (or latest available model)
* **Product Document Structure for Embedding:**
  ```
  "title brand category description features specifications price_range"
  ```
* **Embedding Process:**
  * Concatenate key product fields into searchable text
  * Generate embeddings via OpenAI API during feed sync
  * Store embeddings in Qdrant with product metadata
* **Query Embedding:** User queries embedded in real-time for semantic search

---

**9. Security & Access Control**

* Each tenant has:

  * Unique API token
  * Isolated data environment (index + vector namespace)
* API endpoints protected with token-based auth (Sanctum or Passport)

---

**7. Optional Enhancements**

* Category tree explorer for LLM and UI filters
* Admin dashboard per tenant (usage, indexing status)
* Autocomplete from Meilisearch
* Relevance learning from chat feedback
* Persistent sessions per user (via `chat_sessions` table)

---

**8. Tech Stack**

* Backend: Laravel
* LLM: Neuron AI
* Vector DB: Qdrant (or Chroma)
* Search Engine: Meilisearch
* Authentication: Laravel Sanctum or Passport
* Hosting: Dockerized per service or VPS

---

**9. Next Steps**

* Finalize embedding model selection
* Build feed parser & data normalizer
* Setup Meilisearch + Qdrant with per-tenant support
* Implement chat memory + LLM tool interface
* Launch MVP with conversational UI for one test tenant
