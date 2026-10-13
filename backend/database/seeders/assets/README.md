# Initial website assets

- `logo.jpg`: unchanged copy of the user-supplied root `logo.jpg`; the actual Golden Spike logo.
- `rice-1kg.jpg`, `rice-5kg.jpg`, `rice-10kg.jpg`, `rice-25kg.jpg`: unchanged copies of the saved Facebook promotional artwork already imported into `frontend/public/images`. These contain Arabic promotional text and are seeded with locale `ar`.
- `rice-still-life.png`: the previously approved generated frontend illustration, copied unchanged. This is a food illustration, not a photograph of the business or its actual facilities. See `frontend/docs/ASSETS.md` for its original generation prompt and source details.

Keep these files with the backend deployment. Seeding uses `preservingOriginal()` and imports only empty media collections. Conversions use the application's existing queued media processing. No asset requires a network download.
