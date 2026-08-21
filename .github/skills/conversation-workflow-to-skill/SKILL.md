---
name: conversation-workflow-to-skill
description: 'Create a reusable SKILL.md from chat history. Use when users ask to transform a repeated process into a Copilot skill, define branching decisions, and add quality checks before finalizing.'
argument-hint: 'Outcome + scope + checklist or workflow depth'
user-invocable: true
---

# Conversation Workflow to Skill

## Purpose
Turn a repeated method used in chat into a reusable skill file that can be invoked later.

## When to Use
- User asks to create or improve a SKILL.md.
- A multi-step process appears in the conversation.
- The workflow has decision points and completion checks.
- You want a reusable command instead of repeating guidance manually.

## Inputs to Collect
1. Target outcome produced by the skill.
2. Scope: workspace project skill or personal cross-workspace skill.
3. Format depth: quick checklist or full multi-step workflow.
4. Trigger phrases users are likely to type.

## Procedure
1. Review conversation history and extract a candidate workflow.
2. Identify decision points:
   - If there is no clear workflow, ask for outcome, scope, and depth.
   - If a workflow exists but has gaps, draft assumptions and mark them.
3. Build the first SKILL.md draft:
   - Add YAML frontmatter with matching name and folder.
   - Write keyword-rich description with strong discovery terms.
   - Add concise sections: Purpose, When to Use, Inputs, Procedure, Quality Checks.
4. Save draft in project scope path:
   - .github/skills/<skill-name>/SKILL.md
5. Review weak points and ask focused follow-up questions.
6. Finalize the skill based on answers.
7. Provide usage examples and suggest next related customizations.

## Decision Branching
- No repeatable process found:
  - Ask minimum clarifying questions before editing.
- Process found but inconsistent:
  - Keep stable steps, isolate optional branches, and mark assumptions.
- User unsure about scope:
  - Default to workspace scope unless user requests personal scope.

## Quality Checks
- Name uses lowercase letters, digits, hyphens, and matches folder name.
- Description clearly says what the skill does and when to use it.
- Workflow is actionable and can be executed without hidden context.
- Decision branches are explicit and easy to follow.
- File paths are valid and relative when referencing bundled resources.

## Completion Criteria
- SKILL.md exists at the selected scope path.
- Ambiguities are resolved with explicit user answers.
- Final response includes:
  - What the skill produces
  - Example prompts to invoke it
  - Suggested next customizations

## Example Prompts
- /conversation-workflow-to-skill Create a review workflow skill for PHP bug triage.
- /conversation-workflow-to-skill Build a skill that standardizes regression checks before merge.
- /conversation-workflow-to-skill Turn our attendance module debug routine into a reusable skill.
