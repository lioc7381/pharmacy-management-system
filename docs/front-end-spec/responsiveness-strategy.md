# Responsiveness Strategy

<!--docs/front-end-spec/[title].md-->

While this application is designed exclusively for Android, the platform encompasses a wide variety of screen sizes. Our responsiveness strategy ensures a functional and consistent user experience across all supported devices by adapting the layout to the available screen real estate. Our approach is **mobile-first**, designing for the most constrained view and then allowing the layout to expand on larger screens.


## Breakpoints

We will define two primary breakpoints to guide our layout adaptations. These are based on standard Android density-independent pixel (dp) widths.

| Breakpoint | Min Width | Target Devices |
| :--- | :--- | :--- |
| **Phone** | 0dp | All standard and compact smartphones. |
| **Tablet** | 600dp | Small to large tablets (e.g., Nexus 7, Pixel Tablet, Samsung Galaxy Tab series). |

## Adaptation Patterns

Our strategy focuses on creating a single, flexible UI that reflows gracefully rather than creating separate UIs for each breakpoint. All adaptations must be achievable using core Flutter widgets and the limited dependency set defined in the simplification plan.

*   **Layout Reflowing:** The primary adaptation pattern is reflowing content. Multi-column layouts on tablets will stack into a single, scrollable column on phones. This will be achieved using standard Flutter widgets like `LayoutBuilder` and `GridView`.
    *   **Example (Manager Dashboard):** On a tablet (`>=600dp`), KPI cards will be arranged in a two-column grid. On a phone (`<600dp`), they will stack vertically in a single column.

*   **Navigation:** As mandated by the simplification plan, navigation is managed by the `go_router` package.
    *   For the MVP, a `BottomNavigationBar` is the **exclusive** primary navigation pattern. It will be used consistently across **all** breakpoints, including phones and tablets, to maintain a simple and familiar interaction model. No other navigation patterns (like side rails or drawers) will be implemented.

*   **UI and Interaction Consistency:**
    *   **Touch Targets:** A minimum touch target size of 48x48dp will be strictly enforced across all screen sizes to ensure accessibility and usability.
    *   **Content Priority:** On smaller phone screens, primary content and calls-to-action will be prioritized and visible "above the fold" where possible. Secondary information will be placed further down in the scroll view.
    *   **No Offline Support:** In line with the online-only requirement, the UI will not contain any logic for caching or displaying stale data. If a network request fails, the UI should clearly indicate the failure and provide a mechanism to retry the action.

---