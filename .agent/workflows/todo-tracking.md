---
description: how to track pending/incomplete features when implementing new functionality
---

# Feature Dependency Tracking

When implementing any feature in Intent CMS Pro, follow this workflow to ensure nothing is forgotten:

## During Implementation

1. **Before completing a feature**, check if it depends on other features that don't exist yet
2. **If dependencies are missing**, add them to `TODO.md` in the project root

## Updating TODO.md

Add entries in this format:

```markdown
### Feature Name
- **Status**: Not implemented / Partial / Blocked
- **Required for**: What this enables
- **Dependency**: What must be built first
- **Notes**: Any additional context
```

## After Implementation

1. Move completed features to the "Recently Completed" section
2. Check if the completed feature unblocks any pending features

## File Location

`F:\XAMPP\htdocs\intent-cms-pro\TODO.md`

// turbo-all
