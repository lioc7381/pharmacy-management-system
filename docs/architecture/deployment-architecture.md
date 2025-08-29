# Deployment Architecture

This section defines the process for building and packaging the Pharmacy Management System for distribution. Given the project's explicit constraint as a **local-only, non-production educational capstone**, "deployment" in this context refers to the creation of distributable build artifacts, not deployment to a cloud hosting environment. The strategy prioritizes simplicity, portability, and a security-aware, reproducible process with guaranteed version integrity.

## Versioning Strategy

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

## Secret Management

To produce a release-ready Android APK, it must be cryptographically signed with a private key. Storing this key and its passwords directly in the repository is a critical security vulnerability. Therefore, all sensitive information required for the build process will be managed as encrypted secrets within the project's GitHub repository.

*   **Mechanism:** **GitHub Actions Secrets** will be used to store all sensitive values. These secrets are encrypted and can only be accessed by the GitHub Actions runner during a workflow run.
*   **Required Secrets:** The following secrets must be created in the GitHub repository settings (`Settings > Secrets and variables > Actions`) before a release can be built:
    *   `ANDROID_KEYSTORE_BASE64`: The Java Keystore (`.jks`) file, encoded as a Base64 string.
    *   `ANDROID_KEYSTORE_PASSWORD`: The password for the Keystore file itself.
    *   `ANDROID_KEY_ALIAS`: The alias for the specific key within the Keystore.
    *   `ANDROID_KEY_PASSWORD`: The password for the key alias.

### Generating the Keystore Secret

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

## Continuous Delivery Pipeline (GitHub Actions)

This pipeline automates the testing, building, and releasing of the application. It creates a formal, versioned release on GitHub, attaching the signed Android APK and the production-ready Laravel ZIP archive as downloadable assets.

*   **Continuous Integration (CI):** On every push or pull request to `main`, it runs the full test suite for both applications.
*   **Continuous Delivery (CD):** When a new version tag (e.g., `v1.0.0`) is pushed, it validates version consistency, runs all tests, builds and signs the release artifacts, and then publishes them to a new GitHub Release.

```yaml