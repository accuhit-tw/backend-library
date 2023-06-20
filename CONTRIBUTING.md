# Contributing to Backend Library

## Workflow when contributing a patch

1. Clone the project on GitLab
2. Implement your code changes into separate branch, example: dev_alex
3. Make sure all PHPUnit tests passes and code-style matches PSR-2 (see below). We also have CI builds which will automatically run tests on your pull request. Make sure to fix any reported issues reported by these automated tests.
4. Submit your [merge requests](https://git.accuhit.com.tw/poc/backend-library/-/merge_requests) against `dev` branch, make sure describe your change 

## Building and Testing
Build this project 
```sh
composer install
```

### Run automated code checks
To make sure your code comply with [PSR-2](http://www.php-fig.org/psr/psr-2/) coding style,
tests passes and to execute other automated checks, run locally:
```sh
./vendor/bin/phpcs --standard=PSR2 --runtime-set ignore_warnings_on_exit true --extensions=php src
```

### Unit tests
Please be sure to include tests as appropriate!
```sh
./vendor/bin/phpunit --configuration ./phpunit.xml ./tests
```

### Writing documentation
- Annotation for each function, it is great for writing Docs for Class.  
- Describe how to use in [Readme](./README.md)

## Submitting contribution 
- Namespace would be `namespace Accuhit\BackendLibrary` for all class in `src/` folder.   
- Create a new class for new object inside `src/`, create subfolder if in need.
- Unit test is a easy way to trace your code. 

### Source code directory structure

```bash
<backend-library>/
 ├─ .git/                           # Git configuration and source directory
 └─ src/                            # Main library folder
    ├─ Exceptions/                  # Custom Exception
    ├─ ResponseCode/                # Const of response code 
    ├─ AccuNixApi.php               # AccuNix api 
    └─ ...                          # Create class for each logic
 ├─ tests/                          # Core features tests
 ├─ .env.example                    # Example for .env
 ├─ .gitignore                      # Ignore file 
 ├─ .gitlab-ci.yml                  # CI file
 ├─ CONTRIBUTING.md                 # This file 
 ├─ README.md                       # README file
 └─ ...
```

## Merge requests
 - Merge requests implementing RFCs should be submitted against `dev`.
 - If your pull request exhibits conflicts with the base branch, please resolve them by using `git rebase` instead of `git merge`.

## Release Tips
Project Owner would release if in need.

### Defining Semantic Versioning
Allow rule from [Semantic Versioning 2.0.0](https://semver.org/)

### Create tag
- on shell
```shell
git tags v0.0.1 -m "<your message here>"
#push your tag to remote
git push origin v0.0.1
```

### Write Release Note for tag
[**link**](https://git.accuhit.com.tw/poc/backend-library/-/tags)  
Example
- **Features**
    - feature 1
    - feature 2
    - feature 3
- **Bug Fixes**
    - fix 1
    - fix 2
    - fix 3
- **Dependencies**
    -  {library} from {origin version } to { new version }
