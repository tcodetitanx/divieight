export class ApiHelper {
  /**
   * Gets the error message from a REST API error.
   *
   * @param {any} err The error object.
   * @return {string} The error message.
   */
  public static restGetErrorMessage(err: any): string {
    // Fetch error, e.g. network error.
    if (typeof err === "object" && "error" in err && "status" in err) {
      return err.error;
    }

    // WordPress error, e.g. WP_Error.
    if (typeof err === "object" && "data" in err && "status" in err) {
      if (typeof err.data === "object" && "message" in err.data) {
        return err.data.message;
      }
    }

    return "Unknown Error. Please contact the developer. mpereere@gmail.com";
  }
}
