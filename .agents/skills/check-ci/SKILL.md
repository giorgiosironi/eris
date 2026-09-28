---
name: check-ci
description: Fetch and analyse the latest failed CI run on the current branch. Use when CI is failing or the user asks why a GitHub Actions run failed.
---

# check-ci

Fetch the CI output for the latest failed run on the current branch and load context about what went wrong.

## Steps

### 1. Identify the current branch

```bash
git branch --show-current
```

### 2. Find the latest failed run on this branch

```bash
gh run list --branch <branch> --status failure --limit 5 --json databaseId,name,workflowName,conclusion,createdAt
```

Pick the most recent run with `conclusion: "failure"`. If no failures are found, report that CI is passing and stop.

### 3. List jobs in the failed run

```bash
gh run view <run-id> --json jobs --jq '.jobs[] | {id: .databaseId, name: .name, conclusion: .conclusion}'
```

Identify all jobs with `conclusion: "failure"`.

### 4. Fetch logs for each failed job

```bash
gh run view --log-failed <run-id>
```

This prints only the failing steps across all jobs. Prefer this over fetching individual job logs to keep output concise.

If you need the full log for a specific job:

```bash
gh run view --log <run-id> --job <job-id>
```

### 5. Analyse and summarise

From the logs, extract:

- **Which job(s) failed** (name and matrix parameters if any)
- **Which test or step failed** (test class, method, step name)
- **The error message** (assertion failure, exception, timeout, etc.)
- **Whether this looks flaky** — i.e. the failure is non-deterministic (timing-dependent, random seed, resource contention) rather than caused by a code change

Report a short summary covering these points. If the failure is clearly flaky, say so explicitly and suggest whether it is safe to re-run the workflow without a code fix.
