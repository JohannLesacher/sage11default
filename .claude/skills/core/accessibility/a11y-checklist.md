# Development Checklist — Accessibility (WCAG 2.2 AA)

> Pre-release control checklist. Every component and page must meet these criteria before deployment.

## 1. Adaptable UI

### 1.1. HTML and CSS create a responsive layout

- [ ] Use relative units like em, rem, and percentages for spacing, text, and containers instead of fixed *pixel* units ([1.4.10](https://www.w3.org/WAI/WCAG22/Understanding/reflow), [1.3.4](https://www.w3.org/WAI/WCAG22/Understanding/orientation), [1.4.12](https://www.w3.org/WAI/WCAG22/Understanding/text-spacing), [1.4.4](https://www.w3.org/WAI/WCAG22/Understanding/resize-text))
- [ ] Do not disable *zooming* and *scaling* by adding user-scalable="no" or maximum-scale="1" to the meta *tag* ([1.4.4](https://www.w3.org/WAI/WCAG22/Understanding/resize-text))
- [ ] Ensure that people can *zoom* from 1280 *pixels* to 320 *pixels* and still read and use everything ([1.4.10](https://www.w3.org/WAI/WCAG22/Understanding/reflow)) — **set browser window to 1280px and zoom to 400%**
- [ ] Ensure that people can increase text spacing and still read and use everything ([1.4.12](https://www.w3.org/WAI/WCAG22/Understanding/text-spacing)) — **use tools like [Text Spacing Bookmarklet](https://codepen.io/stevef/full/YLMqbo)**
- [ ] Ensure that people can use landscape or portrait orientation and still read and use everything ([1.3.4](https://www.w3.org/WAI/WCAG22/Understanding/orientation))

## 2. Content Structure

### 2.1. Headings are semantically marked

[W3C headings deep dive](https://www.w3.org/WAI/tutorials/page-structure/headings/)

- [ ] Use heading elements (`<h1>` to `<h6>`) for headings that reflect a logical content hierarchy ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))
- [ ] Ensure that *heading levels* match the intended content structure ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))

### 2.2. Content structure is semantically marked

- [ ] Use `<ol>` and `<li>` elements for *ordered lists* ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))
- [ ] Use `<ul>` and `<li>` elements for *unordered lists* ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))
- [ ] Use the `<q>` element for short *inline quotes* ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))
- [ ] Use the `<blockquote>` element for longer, more complex quotations ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))

## 3. Code Quality

### 3.1. Language and language attributes are identified

- [ ] Add the lang attribute to the `<html>` element, with a relevant language code matching the primary content language ([3.1.1](https://www.w3.org/WAI/WCAG22/Understanding/language-of-page))
- [ ] Add the lang attribute to HTML elements with a relevant language code when the language changes within the content ([3.1.2](https://www.w3.org/WAI/WCAG22/Understanding/language-of-parts))
- [ ] Use the dir attribute, with a value that reflects the text directionality of the element ([3.1.1](https://www.w3.org/WAI/WCAG22/Understanding/language-of-page))

### 3.2. Valid *markup* is more robust

- [ ] Use valid and well-formed HTML and ARIA ([4.1.1](https://www.w3.org/WAI/WCAG22/Understanding/parsing), [1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships)) — in WCAG 2.2, success criterion 4.1.1 on HTML markup validation has become obsolete and is no longer required

## 4. Images

### 4.1. Decorative images are hidden from assistive technologies

- [ ] Add `alt=""` to the `<img>` element, or ([1.1.1](https://www.w3.org/WAI/WCAG22/Understanding/non-text-content))
- [ ] Use CSS background-image or background to add decorative images, or (1.1.1)
- [ ] Add `aria-hidden="true"` and `focusable="false"` to the `<svg>` element, or ([1.1.1](https://www.w3.org/WAI/WCAG22/Understanding/non-text-content))
- [ ] Add `aria-hidden="true"` to HTML elements used to present *icon fonts* ([1.1.1](https://www.w3.org/WAI/WCAG22/Understanding/non-text-content))

### 4.2. Informative images have *alt text*

- [ ] Use the alt attribute to add *alt text* to the `<img>` element ([1.1.1](https://www.w3.org/WAI/WCAG22/Understanding/non-text-content))
- [ ] Add `role="img"` and `aria-label` to add *alt text* to the `<svg>` element ([1.1.1](https://www.w3.org/WAI/WCAG22/Understanding/non-text-content))

## 5. Tables

### 5.1. Associate *caption* and *summary* with their table

- [ ] Use the `<caption>` element to associate a caption with its table, or ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))
- [ ] Add the `aria-labelledby` attribute to the `<table>` element, with a value set to the *ID* of the element containing the caption text ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))
- [ ] Add the `aria-describedby` attribute to the `<table>` element, with the value set to the *ID* of the element containing the visual text summary ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))

### 5.2. Associate data cells with headers

- [ ] Use the `scope` attribute to associate table headers with their row and column ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))
- [ ] Use the `scope` attribute to associate complex table headers with row or column groups ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))
- [ ] Use the `headers` and `ID` attributes to associate complex table headers with data cells when the scope attribute is not sufficient ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))

### 5.3. Data tables identify row and column headers

- [ ] Use the `<table>` element for tabular data ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))
- [ ] Use the `<th>` element for table headers and the `<td>` element for table data ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))

## 6. Forms

### 6.1. Related form fields are grouped

- [ ] Verify that related form fields are grouped using the `<fieldset>` element or the ARIA group or radiogroup *roles* ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))
- [ ] Label grouped fields with the `<legend>` element (for *fieldset*) or `aria-label`/`aria-describedby` (for ARIA group or radiogroup) ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))

### 6.2. Form labels are identified in code

- [ ] Ensure that labels are associated with form fields in code — via `for="{field-id-value}"` attribute ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))
- [ ] Verify that all required fields are indicated in code — `required` attribute and/or [`aria-required`](https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Reference/Attributes/aria-required) ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))

### 6.3. Forms are easy to complete

- [ ] Use the `autocomplete` attribute, where possible, when people enter their own information ([1.3.5](https://www.w3.org/WAI/WCAG22/Understanding/identify-input-purpose))

### 6.4. Errors and *help text* are marked

- [ ] Associate *help text* with form fields using `aria-describedby` ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))
- [ ] Add additional *on-submit validation* if validation only occurs *on-blur*
- [ ] Avoid using `aria-live` for *inline error messages*

## 7. Text Styles

### 7.1. Text formatting is semantically marked

- [ ] Use semantic elements like `<u>`, `<strong>`, and `<em>` to emphasize text ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))

## 8. Keyboard

### 8.1. UI components have a visible *focus* outline

- [ ] Do not use `:focus { outline: none }` to remove the default *focus* outline without creating an alternative ([2.4.7](https://www.w3.org/WAI/WCAG22/Understanding/focus-visible)) — example focus style: `outline: 3px solid #000000; outline-offset: 1px; box-shadow: 0 0 0 6px #ffffff;`

### 8.2. Keyboard *focus* is not trapped

- [ ] Ensure that keyboard *focus* can move to and from all interactive UI components without getting trapped ([2.1.2](https://www.w3.org/WAI/WCAG22/Understanding/no-keyboard-trap))
- [ ] Ensure that elements that are not visible or interactive are also not *focusable* ([2.1.2](https://www.w3.org/WAI/WCAG22/Understanding/no-keyboard-trap))

### 8.3. All components are usable with a keyboard

- [ ] Ensure that the *focus* order defined by the UX matches the DOM order ([2.1.1](https://www.w3.org/WAI/WCAG22/Understanding/keyboard))
- [ ] Use native HTML elements for UI components, which are already keyboard accessible ([2.1.1](https://www.w3.org/WAI/WCAG22/Understanding/keyboard))
- [ ] Do not use the tabindex attribute with positive values — `tabindex="-1"` makes the element focusable but not tabbable (receives focus only from JS); `tabindex="0"` makes the element focusable and tabbable

### 8.4. All tasks can be performed using only a keyboard

- [ ] When a UI component receives keyboard *focus*, it is at least partially visible ([2.4.11](https://www.w3.org/WAI/WCAG22/Understanding/focus-not-obscured-minimum))
- [ ] Minimize the degree (how much) and frequency (how often) a UI component is obscured when it receives keyboard *focus* ([2.4.11](https://www.w3.org/WAI/WCAG22/Understanding/focus-not-obscured-minimum))

## 9. Links

- [ ] Avoid having *links* open in a new browser window or *tab* by default ([3.2.2](https://www.w3.org/WAI/WCAG22/Understanding/on-input)) — if necessary, specify in the accessible name that a new tab or browser window will be opened (optionally indicate this visually with an icon)
- [ ] Avoid assigning a *button* role to *links* using the `<a>` element

## 10. Page Navigation

- [ ] Add a descriptive `<title>` element to every page or view ([2.4.2](https://www.w3.org/WAI/WCAG22/Understanding/page-titled))
- [ ] Identify common page regions with HTML section elements and *ARIA landmark* roles ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships)) — [Page regions](https://www.w3.org/WAI/tutorials/page-structure/regions/)
- [ ] Label repeated landmarks or page regions using `aria-label` or `aria-labelledby` ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships)) — [Labelling regions](https://www.w3.org/WAI/tutorials/page-structure/labels/)
- [ ] Add a descriptive `title` attribute to all `<frame>` and `<iframe>` elements ([1.3.1](https://www.w3.org/WAI/WCAG22/Understanding/info-and-relationships))

### 10.1. *Skip links* are accessible to everyone

- [ ] Ensure that the *skip link* is visible when it receives keyboard *focus* ([2.4.1](https://www.w3.org/WAI/WCAG22/Understanding/bypass-blocks)) — [Skip links](https://www.w3.org/WAI/test-evaluate/easy-checks/skip-link/)
- [ ] Confirm that activating the *skip link* moves *focus* to the beginning of the main content ([2.4.1](https://www.w3.org/WAI/WCAG22/Understanding/bypass-blocks))

## 11. Pointer and Motion Interaction

For keyboard patterns, refer to the [ARIA Authoring Practices Guide (APG)](https://www.w3.org/WAI/ARIA/apg/patterns/)

### 11.1. Newly revealed content is dismissible, hoverable, and persistent

- [ ] Ensure that new content can be dismissed without moving the pointer or keyboard *focus*, for example by using the *Escape* key ([1.4.13](https://www.w3.org/WAI/WCAG22/Understanding/content-on-hover-or-focus))
- [ ] Ensure that the pointer can be moved over the new content without the content disappearing ([1.4.13](https://www.w3.org/WAI/WCAG22/Understanding/content-on-hover-or-focus))
- [ ] Ensure that new content remains visible until keyboard *focus* is moved away from the trigger control, or the new content is dismissed or is no longer relevant ([1.4.13](https://www.w3.org/WAI/WCAG22/Understanding/content-on-hover-or-focus))

## 12. Status Message

- [ ] Ensure that the message container has `role="status"` so it is available to assistive technologies ([4.1.3](https://www.w3.org/WAI/WCAG22/Understanding/status-messages))
