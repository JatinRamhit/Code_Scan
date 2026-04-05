public class CommandRunner {
    public String executeSafeCommand(String action) throws Exception {
        // Only allow specific, predefined operations
        if ("check_status".equals(action)) {
            ProcessBuilder pb = new ProcessBuilder("systemctl", "status", "myapp");
            Process p = pb.start();
            // Read output stream...
            return "Status checked.";
        }
        return "Unauthorized action.";
    }
}
