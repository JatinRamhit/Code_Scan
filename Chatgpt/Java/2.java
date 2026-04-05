import java.nio.charset.StandardCharsets;
import java.nio.file.*;

public class SafeFileLoader {
    private static final Path BASE_DIR = Paths.get("safe_files").toAbsolutePath().normalize();

    public static void main(String[] args) {
        try {
            Files.createDirectories(BASE_DIR);
            Path sample = BASE_DIR.resolve("example.txt");
            if (!Files.exists(sample)) {
                Files.writeString(sample, "This is a safe example file.\n", StandardCharsets.UTF_8);
            }

            String fileName = args.length > 0 ? args[0] : "example.txt";
            Path target = BASE_DIR.resolve(fileName).normalize();

            if (!target.startsWith(BASE_DIR)) {
                System.out.println("Access denied.");
                return;
            }

            if (!Files.exists(target) || !Files.isRegularFile(target)) {
                System.out.println("File not found.");
                return;
            }

            String content = Files.readString(target, StandardCharsets.UTF_8);
            System.out.println("File content:");
            System.out.println(content);

        } catch (Exception e) {
            System.out.println("Error reading file.");
        }
    }
}
