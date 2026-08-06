## Branching Strategy

We follow a three-tier branching model: **feature branches → dev → main**

### Branches

- **`main`** — production-ready code only. Always stable and deployable.
- **`dev`** — integration branch. All finished features are merged here first for testing before going to `main`.
- **`feature/<name>`** — individual work branches. Each team member works in their own feature branch.

### Workflow

1. **Creat your feature branch**

2. **Work and commit on your branch**

3. **Open a Pull Request into `dev`** (not `main`) once your feature is complete and tested.

4. **After review/approval**, merge into `dev`.

5. **Periodically**, once `dev` is stable and tested as a whole, the team lead opens a PR from `dev` → `main` to release.

### Rules

- Never commit directly to `main` or `dev`.
- Always pull the latest `dev` before branching off.
- Open a PR for every merge — no direct pushes to shared branches.
- Resolve merge conflicts locally before opening a PR.
