# External APIs

<!--docs/architecture/[title].md-->

Based on a thorough review of the PRD and technical requirements, the Minimum Viable Product (MVP) for the Pharmacy Management System **does not require any external API integrations**.

This architectural decision is a direct consequence of the project's core constraints:

1.  **Zero-Dollar Budget:** The project must be deliverable with no reliance on paid third-party services.
2.  **Portability & Simplicity:** The system is designed to be fully self-contained and runnable in a local development environment without external dependencies.
3.  **Focused Scope:** The MVP is tightly focused on core, internal workflows. Features that often require external services, such as push notifications (e.g., Firebase Cloud Messaging - FCM), are explicitly listed as "Nice-to-have" or post-MVP enhancements.

Should the project evolve beyond the MVP to include features like push notifications, payment gateways, or other third-party services, this section would be updated to document the integration details, including API endpoints, authentication methods, and data contracts. For the current scope, however, all functionality will be handled by the internal Laravel API.

---
