import { useFetchAttachSignatureToBookingMutation } from "../rtk/myapi";

export default class BookingServiceHelper {
  public static async updateSignature(bookingId: number, signature: string) {
    const [attachSignatureToBooking] =
      await useFetchAttachSignatureToBookingMutation();
    return attachSignatureToBooking({
      bookingId,
      signatureUrl: signature,
    }).unwrap();
  }
}
