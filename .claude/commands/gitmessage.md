Run these two commands and use the output to generate a commit message:
1. `git branch --show-current` — to get the branch name
2. `git diff --staged .` — to get the staged changes

Generate a commit message in this exact format:
`[branchname] MESSAGE_GOES_HERE`

The message should be concise (under 72 characters total), written in imperative mood, and describe what the change does.

Rules:
- Never include Claude or any Co-Authored-By trailer in the commit message.
- Only output the commit message. Do not run any git commands.