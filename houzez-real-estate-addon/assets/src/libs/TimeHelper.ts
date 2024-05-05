export default class TimeHelper {
  public static addMinutesToTime(time: number, minutes: number = 0): number {
    // Extract the hours and minutes from the input time
    const hours: number = Math.floor(time / 100);
    const minutesInTime: number = time % 100;

    // Add the specified minutes
    const totalMinutes: number = hours * 60 + minutesInTime + minutes;

    // Calculate the new hours and minutes
    const newHours: number = Math.floor(totalMinutes / 60);
    const newMinutes: number = totalMinutes % 60;

    // Format the new time
    return newHours * 100 + newMinutes;
  }

  public static convertNumberToTime(number: number): string {
    const hours = Math.floor(number / 100);
    const minutes = number % 100;
    const formattedHours = hours.toString().padStart(2, "0");
    const formattedMinutes = minutes.toString().padStart(2, "0");
    const result = `${formattedHours}:${formattedMinutes}`;
    return result;
  }

  public static convertTimeToNumber(time: string): number {
    const [hours, minutes] = time.split(":").map(Number);
    const result = hours * 100 + minutes;
    return result;
  }
}
