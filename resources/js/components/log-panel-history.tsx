import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from "@/components/ui/collapsible";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { ScrollArea } from "@/components/ui/scroll-area";
import { ChevronDown, ChevronUp } from "lucide-react";
import { useMemo, useState } from "react";

export type Log = {
    id: number;
    message: string | null;
    operation: string;
    changed_by: { id: number; name: string } | null;
    created_at: string;
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    old_data: Record<string, any> | null;
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    new_data: Record<string, any> | null;
};

export default function LogHistoryPanel({ logs }: { logs: Log[] }) {
    const [filter, setFilter] = useState("");
    const [selected, setSelected] = useState<Log | null>(null);

    const filteredLogs = useMemo(() => {
        if (!filter) return logs;
        return logs.filter((log) =>
            log.message?.toLowerCase().includes(filter.toLowerCase())
        );
    }, [logs, filter]);

    const [open, setOpen] = useState(false)

    const showFilter = false;

    return (
        <Collapsible open={open} onOpenChange={setOpen} className="mt-8 rounded-2xl shadow-sm">

            <Card className="mt-8 rounded-2xl shadow-sm">
                <CardHeader>
                    <CardTitle className="flex items-center justify-between">
                        <div className="flex items-center gap-2">
                            <span>Log History</span>
                            <CollapsibleTrigger asChild>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    className="h-6 w-6"
                                >
                                    {open ? <ChevronUp className="h-4 w-4" /> : <ChevronDown className="h-4 w-4" />}
                                </Button>
                            </CollapsibleTrigger>
                        </div>
                        {showFilter && (
                            <Input
                                placeholder="Filter by message..."
                                value={filter}
                                onChange={(e) => setFilter(e.target.value)}
                                className="max-w-xs"
                            />
                        )}
                    </CardTitle>
                </CardHeader>
                <CollapsibleContent>
                    <CardContent>
                        <ScrollArea className="h-[200px]">
                            <ul className="divide-y">
                                {filteredLogs.length === 0 && (
                                    <li className="p-4 text-sm text-muted-foreground text-center">
                                        No logs found
                                    </li>
                                )}
                                {filteredLogs.map((log) => (
                                    <li
                                        key={log.id}
                                        className="flex items-center justify-between p-4 hover:bg-muted/30 cursor-pointer"
                                        onClick={() => setSelected(log)}
                                    >
                                        <div>
                                            <p className="font-medium">{log.message ?? "—"}</p>
                                            <div className="text-xs text-muted-foreground space-x-2">
                                                <span>{log.changed_by?.name ?? "System"}</span>
                                                <span>•</span>
                                                <span>
                                                    {new Date(log.created_at).toLocaleString("id-ID")}
                                                </span>
                                            </div>
                                        </div>
                                        <Badge variant="outline">{log.operation}</Badge>
                                    </li>
                                ))}
                            </ul>
                        </ScrollArea>
                    </CardContent>
                </CollapsibleContent>

                {/* Detail Modal */}
                <Dialog open={!!selected} onOpenChange={() => setSelected(null)}>
                    <DialogContent className="w-full sm:max-w-4xl">
                        <DialogHeader>
                            <DialogTitle>Log Detail</DialogTitle>
                            <DialogDescription>
                                {selected?.message ?? "No message"}
                            </DialogDescription>
                        </DialogHeader>
                        {selected && (
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 h-[50vh]">
                                <div className="border rounded-lg p-4 bg-muted/30 overflow-auto">
                                    <h3 className="font-semibold mb-2">Old Data</h3>
                                    <pre className="text-xs whitespace-pre-wrap">
                                        {JSON.stringify(selected.old_data, null, 2)}
                                    </pre>
                                </div>
                                <div className="border rounded-lg p-4 bg-muted/30 overflow-auto">
                                    <h3 className="font-semibold mb-2">New Data</h3>
                                    <pre className="text-xs whitespace-pre-wrap">
                                        {JSON.stringify(selected.new_data, null, 2)}
                                    </pre>
                                </div>
                            </div>
                        )}
                        <div className="flex justify-end mt-4">
                            <Button variant="secondary" onClick={() => setSelected(null)}>
                                Close
                            </Button>
                        </div>
                    </DialogContent>
                </Dialog>
            </Card>
        </Collapsible>
    );
}
