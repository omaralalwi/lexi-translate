# Changelog

All notable changes to `lexi-translate` will be documented in this file

## 1.0.13 - 2026-10-30

- Fix the `lexi_posts` test-table migration: `down()` dropped `test_translations` instead of `lexi_posts`, so a rollback left the table behind and the next migrate failed with "table already exists".

## 1.0.0 - 201X-XX-XX

- initial release
