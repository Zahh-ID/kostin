# AuthController

**Namespace**: `App\Http\Controllers\Api\V1`

## Description
Handles standard user authentication including registration, login, logout, and retrieving the current user.

## Methods

### `register`
- **Method**: POST
- **Path**: `/api/v1/auth/register`
- **Description**: Registers a new user (tenant, owner, or admin).
- **Parameters**:
    - `name`: string, required
    - `email`: string, email, required
    - `password`: string, min 8 chars, required
    - `role`: string (tenant, owner, admin), required

### `login`
- **Method**: POST
- **Path**: `/api/v1/auth/login`
- **Description**: Authenticates a user and returns an API token.
- **Parameters**:
    - `email`: string, required
    - `password`: string, required

### `logout`
- **Method**: POST
- **Path**: `/api/v1/auth/logout`
- **Description**: Revokes the current user's access token.

### `me`
- **Method**: GET
- **Path**: `/api/v1/auth/me`
- **Description**: Returns the currently authenticated user's details.
