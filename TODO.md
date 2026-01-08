# Intent CMS Pro - TODO / Backlog

> This file tracks features that are not yet implemented or require additional work.
> **AI Rule**: When implementing a feature that depends on another feature, add the dependency here.

---

## 🚧 Settings Features (Pending)

### Permalinks Integration
- **Status**: Not implemented
- **Required for**: Custom URL structures for posts/pages
- **Dependency**: Router-level URL rewriting support
- **Notes**: The admin UI saves permalink_structure setting, but it's not used by the routing system yet.

### Discussion Settings
- **Status**: Not implemented  
- **Required for**: Comment moderation, enabling/disabling comments
- **Dependency**: Comment system must be built first
- **Settings involved**: `comments_enabled`, `comment_moderation`

### Homepage Display Option
- **Status**: Not implemented
- **Required for**: Choosing between "latest posts" or "static page" as homepage
- **Dependency**: Needs `show_on_front` logic in `home.php`
- **Notes**: Setting exists but isn't used yet.

---

## 📋 Feature Dependencies

| Feature | Depends On | Priority |
|---------|-----------|----------|
| Permalinks | Router URL rewriting | Medium |
| Comments | Comment table + API | High |
| show_on_front | Page selector in settings | Low |

---

## ✅ Recently Completed

- [x] Settings Management with tabbed UI (2026-01-08)
- [x] Site title/tagline frontend integration (2026-01-08)
- [x] Posts per page setting (2026-01-08)
- [x] Date format setting (2026-01-08)
- [x] Media sizes in thumbnail generation (2026-01-08)

---

*Last updated: 2026-01-08*
