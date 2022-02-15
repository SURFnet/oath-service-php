# Changelog

## [1.2.2](https://github.com/SURFnet/oath-service-php/tree/1.2.0) (2022-01-15)

### Fixes

- Only return valid http-status-codes if an error occures
- Send logging to syslog

## [1.1.0](https://github.com/SURFnet/oath-service-php/tree/1.1.0) (2022-01-03)

Upgrade to Symfony 4.4 LTS
Symfony was upgraded to 4.4 this can be globally be drilled down into
several individual tasks that have (sadly) not been grouped in separate
commits. But for the sake of keeping the upgrade process more
streamlined, a monster commit was created. Here are the most notable
updates:

1. Config was moved from app/config to the Symfony Flex based /config setup
2. Deprecations have been addressed, this included rewriting some of the
   dependency injection (or lack thereof) constructions
3. Other dependencies have been updated to a version compatible with
   Symfony 4.
4. The Docker development and testing containers have been reviewed and
   where tweaked slightly. Mainly to be able to wrap my own brain around
   the way things where set up.
5. A development document was added to the /docs folder
6. .env files have been added with sensible defaults
7. Composer and Symfony lockfiles are now included in the project
8. The gitignore file was updated to be more SF4 oriented
9. Added a editorconfig for better complience with conde styles

## [1.0.1](https://github.com/SURFnet/oath-service-php/tree/1.0.1) (2015-07-23)

[Full Changelog](https://github.com/SURFnet/oath-service-php/compare/1.0.0-20150612142526Z-b818c2463a68e874d0ab1cbeac3197cf92901996...1.0.1)

## [1.0.0](https://github.com/SURFnet/oath-service-php/tree/1.0.0) (2015-06-12)

[Full Changelog](https://github.com/SURFnet/oath-service-php/compare/develop-20150612142526Z-b818c2463a68e874d0ab1cbeac3197cf92901996...1.0.0)

## [develop-20150612142526Z-b818c2463a68e874d0ab1cbeac3197cf92901996](https://github.com/SURFnet/oath-service-php/tree/develop-20150612142526Z-b818c2463a68e874d0ab1cbeac3197cf92901996) (2015-06-12)

[Full Changelog](https://github.com/SURFnet/oath-service-php/compare/de8de2d39301dd2aef9a67c337e32086f0a01273...develop-20150612142526Z-b818c2463a68e874d0ab1cbeac3197cf92901996)

**Merged pull requests:**

- Made Yubi pull mergeable by merging upstream changes in README. [\#3](https://github.com/SURFnet/oath-service-php/pull/3) ([ijansch](https://github.com/ijansch))
- Adds support for YubiHSM [\#2](https://github.com/SURFnet/oath-service-php/pull/2) ([mkoppanen](https://github.com/mkoppanen))
- Initial setup and first implementation for pdo storage of secrets [\#1](https://github.com/SURFnet/oath-service-php/pull/1) ([lineke](https://github.com/lineke))
