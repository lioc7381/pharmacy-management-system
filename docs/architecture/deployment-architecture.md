## Deployment Architecture

This section defines the process for building and packaging the Pharmacy Management System for distribution. Given the project's explicit constraint as a **local-only, non-production educational capstone**, "deployment" in this context refers to the creation of distributable build artifacts, not deployment to a cloud hosting environment. The strategy prioritizes simplicity, portability, and a security-aware, reproducible process with guaranteed version integrity.

### Versioning Strategy

To ensure traceability and prevent ambiguity between the source code and the released artifacts, the project will adhere to a strict versioning policy.

*   **Source of Truth:** The version number defined in `frontend/pubspec.yaml` is the **single source of truth** for the entire project.
*   **Format:** The version will follow the Semantic Versioning standard (`MAJOR.MINOR.PATCH`). The Git tag used to trigger a release **must** match this version, prefixed with a `v`.
    *   `pubspec.yaml`: `version: 1.0.0+1`
    *   Git Tag: `v1.0.0`
*   **Release Workflow:** To create a new release, a developer **must** follow this sequence:
    1.  Update the `version` in `frontend/pubspec.yaml`.
    2.  Commit this change to Git.
    3.  Create a new Git tag that exactly matches the new version (e.g., `git tag v1.0.0`).
    4.  Push both the commit and the tag to the repository.

The CI/CD pipeline will automatically enforce this policy, failing the build if the Git tag and the `pubspec.yaml` version do not match.

### Secret Management

To produce a release-ready Android APK, it must be cryptographically signed with a private key. Storing this key and its passwords directly in the repository is a critical security vulnerability. Therefore, all sensitive information required for the build process will be managed as encrypted secrets within the project's GitHub repository.

*   **Mechanism:** **GitHub Actions Secrets** will be used to store all sensitive values. These secrets are encrypted and can only be accessed by the GitHub Actions runner during a workflow run.
*   **Required Secrets:** The following secrets must be created in the GitHub repository settings (`Settings > Secrets and variables > Actions`) before a release can be built:
    *   `ANDROID_KEYSTORE_BASE64`: The Java Keystore (`.jks`) file, encoded as a Base64 string.
    *   `ANDROID_KEYSTORE_PASSWORD`: The password for the Keystore file itself.
    *   `ANDROID_KEY_ALIAS`: The alias for the specific key within the Keystore.
    *   `ANDROID_KEY_PASSWORD`: The password for the key alias.

#### Generating the Keystore Secret

This subsection provides the exact commands needed to generate the keystore file and encode it for use as a GitHub secret.

**1. Generate the Keystore File:**

Run the following command from your terminal. This will create a file named `upload-keystore.jks` in your current directory.

```bash
keytool -genkeypair -v -keystore upload-keystore.jks -keyalg RSA -keysize 2048 -validity 10000 -alias upload
```

The command will prompt you for several pieces of information:
*   **"Enter keystore password:"** This is the password for the file itself. Use this value for the `ANDROID_KEYSTORE_PASSWORD` secret.
*   **Distinguished Name Information:** (Name, Organization, etc.). You can fill these out as you see fit.
*   **"Enter key password for `<upload>`:"** This is the password for the key alias. Use this value for the `ANDROID_KEY_PASSWORD` secret. (It's common practice to use the same password as the keystore password).

**2. Base64-Encode the Keystore File:**

GitHub secrets cannot store binary files, so you must convert the `.jks` file into a single string using Base64 encoding.

*   **On Linux or macOS:**
    ```bash
    base64 upload-keystore.jks
    ```
*   **On Windows (Command Prompt):**
    ```bash
    certutil -encode upload-keystore.jks keystore.b64 && type keystore.b64
    ```

Copy the entire output string from the command and paste it into the `ANDROID_KEYSTORE_BASE64` GitHub secret.

### Continuous Delivery Pipeline (GitHub Actions)

This pipeline automates the testing, building, and releasing of the application. It creates a formal, versioned release on GitHub, attaching the signed Android APK and the production-ready Laravel ZIP archive as downloadable assets.

*   **Continuous Integration (CI):** On every push or pull request to `main`, it runs the full test suite for both applications.
*   **Continuous Delivery (CD):** When a new version tag (e.g., `v1.0.0`) is pushed, it validates version consistency, runs all tests, builds and signs the release artifacts, and then publishes them to a new GitHub Release.

```yaml
# .github/workflows/ci.yaml
name: Continuous Integration and Delivery

on:
  push: { branches: [ "main" ] }
  pull_request: { branches: [ "main" ] }
  create: { tags: [ 'v*.*.*' ] }

jobs:
  # --- TEST JOBS (Run on every push/PR to main) ---
  test-backend:
    runs-on: ubuntu-latest
    defaults: { run: { working-directory: ./backend } }
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with: { php-version: '8.3', extensions: 'sqlite3, pdo_sqlite', tools: 'composer' }
      - name: Install Dependencies
        run: composer install --prefer-dist --no-progress
      - name: Prepare Laravel Application
        run: |
          cp .env.example .env
          php artisan key:generate
          touch database/database.sqlite
      - name: Run Migrations
        run: php artisan migrate
      - name: Run Backend Tests
        run: php artisan test

  test-frontend:
    runs-on: ubuntu-latest
    defaults: { run: { working-directory: ./frontend } }
    steps:
      - uses: actions/checkout@v4
      - uses: subosito/flutter-action@v2
        with: { flutter-version: '3.22.x', channel: 'stable' }
      - name: Install Dependencies
        run: flutter pub get
      - name: Run Frontend Tests
        run: flutter test

  # --- BUILD & PACKAGE JOBS (Run only on tag creation) ---
  build-and-package:
    needs: [test-backend, test-frontend]
    if: startsWith(github.ref, 'refs/tags/')
    runs-on: ubuntu-latest
    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Validate Version Tag
        # This step enforces that the Git tag matches the version in pubspec.yaml.
        run: |
          # Extract version from Git tag (e.g., "v1.0.1" -> "1.0.1")
          GIT_TAG_VERSION=${{ github.ref_name#v }}
          
          # Extract version from pubspec.yaml (e.g., "version: 1.0.1+2" -> "1.0.1")
          PUBSPEC_VERSION=$(grep 'version:' frontend/pubspec.yaml | cut -d ' ' -f 2 | cut -d '+' -f 1)
          
          echo "Git Tag Version: $GIT_TAG_VERSION"
          echo "Pubspec Version: $PUBSPEC_VERSION"
          
          if [ "$GIT_TAG_VERSION" != "$PUBSPEC_VERSION" ]; then
            echo "Error: Git tag '$GIT_TAG_VERSION' does not match pubspec.yaml version '$PUBSPEC_VERSION'."
            exit 1
          fi

      # --- Backend Packaging ---
      - name: Setup PHP & Composer
        uses: shivammathur/setup-php@v2
        with: { php-version: '8.3', tools: 'composer' }
      - name: Package Backend
        working-directory: ./backend
        run: |
          composer install --optimize-autoloader --no-dev
          php artisan config:cache
          php artisan route:cache
          zip -r ../backend-release.zip . -x ".git/*" ".env" "storage/logs/*" "tests/*"
      
      # --- Frontend Build & Signing ---
      - name: Setup Flutter
        uses: subosito/flutter-action@v2
        with: { flutter-version: '3.22.x', channel: 'stable' }
      - name: Decode and Install Keystore
        run: |
          echo "${{ secrets.ANDROID_KEYSTORE_BASE64 }}" | base64 --decode > frontend/android/app/upload-keystore.jks
      - name: Create Keystore Properties
        run: |
          echo "storePassword=${{ secrets.ANDROID_KEYSTORE_PASSWORD }}" > frontend/android/key.properties
          echo "keyPassword=${{ secrets.ANDROID_KEY_PASSWORD }}" >> frontend/android/key.properties
          echo "keyAlias=${{ secrets.ANDROID_KEY_ALIAS }}" >> frontend/android/key.properties
          echo "storeFile=upload-keystore.jks" >> frontend/android/key.properties
      - name: Build Signed Release APK
        working-directory: ./frontend
        run: |
          flutter pub get
          flutter build apk --release
      
      # --- Upload Artifacts for Release Job ---
      - name: Upload Backend Artifact
        uses: actions/upload-artifact@v4
        with:
          name: backend-archive
          path: backend-release.zip
      - name: Upload Frontend Artifact
        uses: actions/upload-artifact@v4
        with:
          name: frontend-apk
          path: frontend/build/app/outputs/flutter-apk/app-release.apk

  # --- CREATE GITHUB RELEASE JOB (Run only on tag creation) ---
  create-release:
    needs: build-and-package
    if: startsWith(github.ref, 'refs/tags/')
    runs-on: ubuntu-latest
    permissions:
      contents: write # Required to create a release
    steps:
      - name: Download Backend Artifact
        uses: actions/download-artifact@v4
        with:
          name: backend-archive
      - name: Download Frontend Artifact
        uses: actions/download-artifact@v4
        with:
          name: frontend-apk
      - name: Create GitHub Release
        uses: softprops/action-gh-release@v2
        with:
          # The body of the release will be automatically populated with changes since the last tag.
          generate_release_notes: true
          files: |
            backend-release.zip
            app-release.apk
```

### Environments

| Environment | Frontend URL | Backend URL | Purpose |
| :--- | :--- | :--- | :--- |
| **Local Development** | Android Emulator | `http://10.0.2.2:8000` | Primary environment for all development and testing. |
| **CI/Testing** | N/A (Headless) | N/A (Headless) | Automated testing via the CI/CD pipeline. |
| **Production** | N/A | N/A | **Out of scope for this educational project.** |

---