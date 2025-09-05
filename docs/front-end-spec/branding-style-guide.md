# Branding & Style Guide

<!--docs/front-end-spec/[title].md-->

This section defines the core visual identity of the Pharmacy Management System. Adherence to this guide will ensure a consistent, functional, and clear user interface.

The foundation of our visual style is a **dark Material theme**.

## Visual Identity

*   **Brand Guidelines:** As no formal corporate brand guidelines were provided, this style guide will serve as the foundational visual identity. The aesthetic is clean, functional, and clear, designed to convey trustworthiness in a healthcare context through simplicity and predictability.

## Color Palette

The color palette is designed for a dark theme, prioritizing accessibility (WCAG AA contrast ratios) and clarity. The functional colors are especially important for providing intuitive feedback for user actions within the simplified, online-only architecture.

| Color Type | Hex Code | Usage |
| :--- | :--- | :--- |
| Primary | `#7B8FF7` | Key interactive elements: primary buttons, active tabs, focused inputs. |
| Secondary | `#3A4D9F` | Supporting elements, secondary buttons, and highlights. |
| Accent | `#6DDCCF` | Special highlights, progress indicators, and unique callouts. |
| Success | `#5CB85C` | Positive feedback, confirmations, and success messages. |
| Warning | `#F0AD4E` | Cautions and important, non-critical notices. |
| Error | `#D9534F` | Error messages, destructive action confirmations, and validation failures. **Crucially, this is the primary indicator for network failures.** |
| Neutral | `#FFFFFF` | Primary text color for maximum contrast on dark backgrounds. |
| | `#B0B0B0` | Secondary text (e.g., helper text, timestamps). |
| | `#2C2C2E` | Surface color for cards and modals. |
| | `#1C1C1E` | Primary background color of the application. |

### Communicating Application State

Per the simplification plan, the application is **strictly online-only** and does not proactively check for a network connection. Therefore, the UI must communicate state reactively.

*   **Online State:** This is the default state. All UI elements are interactive.
*   **Loading State:** When data is being fetched, a `CircularProgressIndicator` or similar loading indicator should be displayed.
*   **Error/Offline State:** If a network request fails (due to being offline or another server error), the UI must display a clear message using the `Error` color (`#D9534F`). UI elements should offer a "Retry" action rather than being disabled, as the application cannot know when the connection will be restored.

## Typography

The typography is selected for maximum readability and a clean aesthetic on mobile screens. We will use a single, versatile font family to maintain consistency.

### Font Families

*   **Primary & UI:** **Roboto**. As the standard Material Design font, it ensures excellent legibility and a native feel.
*   **Monospace:** **Roboto Mono** (for displaying reference numbers or any fixed-width data).

### Type Scale

The type scale establishes a clear visual hierarchy for all text content.

| Element | Size | Weight | Line Height |
| :--- | :--- | :--- | :--- |
| H1 | 28pt | Bold | 36pt |
| H2 | 24pt | Bold | 32pt |
| H3 | 20pt | Medium | 28pt |
| Body | 16pt | Regular | 24pt |
| Small | 14pt | Regular | 20pt |
| Button | 16pt | Medium | 20pt |

## Iconography

*   **Icon Library:** We will exclusively use the **Material Icons** library. This ensures visual consistency with the Material Design system and simplifies development.
*   **Usage Guidelines:** The default style will be **"Filled"** for maximum clarity and visual weight, especially in the bottom navigation bar. "Outlined" icons may be used for secondary actions where a lighter visual touch is needed. All icons should be used at standard sizes (e.g., 24dp).

## Spacing & Layout

*   **Grid System:** All layouts will be based on an **8-point grid system**. All spacing and component dimensions will be in multiples of 8 (e.g., 8dp, 16dp, 24dp).
*   **Spacing Scale:** This system provides a consistent rhythm and balance to the UI.
    *   **4dp:** Micro-spacing (e.g., between an icon and its text).
    *   **8dp:** Small spacing (e.g., between related elements).
    *   **16dp:** Medium padding and margins (standard content padding within cards).
    *   **24dp:** Large spacing (e.g., between distinct sections on a screen).
    *   **32dp:** Extra-large spacing for significant visual separation.

---