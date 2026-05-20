# Feature Roadmap

This file breaks the site improvements into small steps so we can work through them in order without losing focus.

## Phase 1: Simplify what already exists

Goal: remove visual noise and repeated UI patterns before adding anything new.

### Step 1: Audit the current pages

- [ ] List all admin, user, and public pages
- [ ] Mark the pages that are used least often
- [ ] Note where the same action appears in more than one place

### Step 2: Remove duplicate UI

- [ ] Keep one navigation pattern per screen
- [ ] Remove repeated action buttons from headers and sidebars
- [ ] Keep one status badge and one history/timeline view
- [ ] Replace page-specific empty states with the shared component

### Step 3: Make internal pages compact

- [ ] Use compact headers on admin and dashboard pages
- [ ] Reduce oversized hero sections on internal screens
- [ ] Tighten spacing where content is repeated or stacked

### Step 4: Verify the cleanup

- [ ] Check the main admin pages on desktop and mobile
- [ ] Confirm the layout still feels clear after removing duplicates
- [ ] Run the existing feature tests for the affected flows

### Phase 1 done when

- [ ] The navigation is simpler on every major screen
- [ ] Internal pages look compact and consistent
- [ ] Duplicate status and empty-state patterns are gone
- [ ] Nothing important was removed from the user flow

## Phase 2: High-ROI user features

- [ ] Add recent search history to the search dropdown
- [ ] Show estimated delivery dates on order cards and details
- [ ] Save filter preferences for frequent shoppers
- [ ] Improve notifications with grouping and batch read actions
- [ ] Add product recommendations based on cart and viewing history

## Phase 3: Admin efficiency

- [ ] Add bulk actions for moderation and cleanup tasks
- [ ] Add faster shortcuts for common admin workflows
- [ ] Review reports and logs for the most common support issues
- [ ] Consolidate rarely used admin tools into a secondary area

## Phase 4: Polish and validation

- [ ] Check mobile spacing on all major pages
- [ ] Verify empty, loading, and error states use the same visual language
- [ ] Confirm buttons, badges, and cards follow the same hierarchy
- [ ] Run the existing feature tests
- [ ] Fix any layout regressions before adding the next feature

## Suggested order

1. Simplify the current UI first.
2. Add the easiest high-value user features.
3. Improve admin speed after the core UX is clean.
4. Finish with polish and testing.

## Notes

- Keep each step small enough to finish in one session.
- If a feature adds complexity without clear user value, postpone it.
- Prefer reuse of shared components instead of page-specific one-off UI.
