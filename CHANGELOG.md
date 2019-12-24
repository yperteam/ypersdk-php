# Changelog


## 3.0.5

New exception `AuthException` inheriting from `YperException` to distinguish 403 HTTP errors, from other errors.

## 4.0.0

- Update PHP version to `7.2`.
- QueryHelper to properly encode in http url different variable types as:
    - `\DateTime`
    - `bool`

## 4.1.0

- Add (non-associative) array encoding support for QueryHelper (GET)
