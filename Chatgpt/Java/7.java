import java.util.HashMap;
import java.util.Map;

public class SafeInputParser {
    public static void main(String[] args) {
        String input = args.length > 0 ? args[0] : "name=Alice;age=22;city=Paris";

        Map<String, String> data = parseKeyValueInput(input);

        System.out.println("Parsed values:");
        for (Map.Entry<String, String> entry : data.entrySet()) {
            System.out.println(entry.getKey() + " = " + entry.getValue());
        }
    }

    private static Map<String, String> parseKeyValueInput(String input) {
        Map<String, String> result = new HashMap<>();

        for (String pair : input.split(";")) {
            String[] parts = pair.split("=", 2);
            if (parts.length == 2) {
                String key = parts[0].trim();
                String value = parts[1].trim();

                if (!key.isEmpty() && key.matches("[a-zA-Z0-9_]+")) {
                    result.put(key, value);
                }
            }
        }

        return result;
    }
}
