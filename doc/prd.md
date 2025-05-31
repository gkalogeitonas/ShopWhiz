**Project Requirements Document (PRD)**

**Project Title:**
Multi-Tenant AI-Powered Conversational Product Assistant for E-Shops

---

**1. Objective**

Develop an external, multi-tenant service that allows e-shops (using OpenCart or similar platforms) to integrate a chat-based product assistant via API. The assistant uses LLMs to interpret customer queries, replacing traditional filters with a conversational experience. It combines semantic search (vector database) with structured filtering (Meilisearch) for highly relevant product discovery.

---

**2. Key Features**

- Conversational assistant that guides users in filtering product listings
- LLM-powered natural language understanding and reasoning
- Structured filtering via Meilisearch with count-only access for the LLM
- Frontend (user app) receives full results for the same query
- Semantic enrichment via vector database (to be introduced in Phase 2)
- Multi-tenant support with API token authentication


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
* Admin users authenticate through Laravel's built-in authentication system

---

**4. Chat Interaction Flow (Conversational Filtering UX)**

1. **User Input:** Customer types a natural request
2. **LLM Request:** Message sent to Neuron AI
3. **Category/Brand Reference:**
   - LLM uses known lists of categories and brands to reason
4. **Query Generation:**
   - LLM builds structured query: `{"search": "κουρτίνα μπάνιου", "brand": "San Lorentzo", "price < 20"}`
5. **Count Check Only:**
   - Tool call: `getProductCount(filters)`
   - Returns `product_count = 12`
6. **Frontend Request:**
   - Sends same query to `/api/products/search`
   - Receives full product results to render to the user
7. **Conversation Continuation:**
   - LLM provides message and follow-up suggestions


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



**7. Embedding Strategy**

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

**8. Security & Access Control**

* Each tenant has:

  * Unique API token
  * Isolated data environment (index + vector namespace)
* API endpoints protected with token-based auth (Sanctum or Passport)

---

**9. Admin Dashboard & User Interface Architecture**

### 7.1 Architecture Approach

The application will follow a hybrid architecture with two distinct frontends:

* **Chat Widget**: Fully decoupled Vue.js SPA consuming the API endpoints
* **Admin Dashboard**: Inertia.js + Vue.js monolith integrated with Laravel backend

This hybrid approach provides:
* Simplified authentication and state management for the admin section
* Complete decoupling for the customer-facing chat widget
* Reuse of backend validation and business logic in the admin interface

### 7.2 Admin Dashboard Features

* Tenant registration and management
* API token generation and management
* Feed configuration (URL, format, sync schedule)


### 7.3 Chat Widget Interface

* Standalone Vue.js component that can be embedded in e-shops
* Communicates with ShopWhiz backend via API
* Responsive design for mobile and desktop

---

**10. Optional Enhancements**

- Category tree explorer for LLM and UI filters
- Autocomplete from Meilisearch
- Relevance learning from chat feedback
- Persistent sessions per user (via `chat_sessions` table)
- Phase 2: Semantic category suggestion using vector database
  - Vector DB used to match user query to products
  - LLM samples most common `product_type` values in top semantic matches
  - Used to propose or auto-select category filters before calling `getProductCount`
  - Products still not returned directly to the LLM


* Category tree explorer for LLM and UI filters
* Autocomplete from Meilisearch
* Relevance learning from chat feedback
* Persistent sessions per user (via `chat_sessions` table)

---

**11. Tech Stack**

* Backend: Laravel
* Frontend (Admin Dashboard): Inertia.js + Vue.js
* Frontend (Chat Widget): Vue.js (standalone)
* LLM: Neuron AI
* Vector DB: Qdrant (or Chroma)
* Search Engine: Meilisearch
* Authentication: Laravel Sanctum or Passport
* Hosting: Dockerized per service or VPS

---

**10. Next Steps**

- Finalize embedding model selection
- Build feed parser & data normalizer
- Setup Meilisearch with per-tenant support
- Implement chat memory + LLM tool interface
- Setup Inertia.js for admin dashboard
- Launch MVP with conversational UI for one test tenant
- Implement `getProductCount` Meilisearch tool
- Restrict LLM to metadata-level access only (count, filters)
- Phase 2: Integrate vector database for category guidance
- Add category frequency analysis on top semantic hits
